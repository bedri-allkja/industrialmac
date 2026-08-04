<?php

namespace App\Services;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Childcategory;
use App\Models\Currency;
use App\Models\Product;
use App\Models\ProductImport;
use App\Models\Subcategory;
use DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Image;

class ProductImportService
{
    private const LARGE_FILE_BYTES = 10 * 1024 * 1024;

    private array $brandCache = [];

    private array $categoryCache = [];

    private array $subcategoryCache = [];

    private array $childcategoryCache = [];

    private array $skuCache = [];

    private bool $skipImages = false;

    private float $currencyValue = 1;

    private int $progressEvery = 5;

    public function run(ProductImport $import): void
    {
        @set_time_limit(0);
        $this->skipImages = (bool) $import->skip_images;
        @ini_set('memory_limit', $this->skipImages ? '512M' : '1024M');
        $isResume = (int) $import->last_row > 0;

        $statusUpdate = [
            'status' => 'running',
            'finished_at' => null,
            'message' => $isResume
                ? __('Resuming from row :row...', ['row' => $import->last_row + 1])
                : __('Import starting...'),
        ];

        if (! $import->started_at) {
            $statusUpdate['started_at'] = now();
        }

        $import->update($statusUpdate);

        try {
            if (! is_file($import->file_path)) {
                throw new \RuntimeException(__('Import file not found.'));
            }

            $isLargeFile = filesize($import->file_path) > self::LARGE_FILE_BYTES;

            if (! $isResume) {
                if ($isLargeFile) {
                    $import->update([
                        'total_rows' => 0,
                        'message' => __('Import started. Processing large file...'),
                    ]);
                } else {
                    $import->update(['message' => __('Counting rows...')]);
                    $import->update([
                        'total_rows' => max(0, $this->countDataRows($import->file_path)),
                        'message' => __('Import started.'),
                    ]);
                }
            }

            $this->initializeCaches();
            $handle = $this->openImportCsv($import->file_path);
            $rowNumber = 1;
            $resumeFrom = (int) $import->last_row;

            while (($line = fgetcsv($handle)) !== false) {
                if ($rowNumber === 1) {
                    $rowNumber++;
                    continue;
                }

                if ($rowNumber <= $resumeFrom) {
                    $rowNumber++;
                    continue;
                }

                $this->processRow($import, $line, $rowNumber);
                $rowNumber++;
            }

            fclose($handle);
            $this->clearCaches();

            $finalProcessed = max((int) $import->processed_rows, $rowNumber - 2);

            $import->update([
                'status' => 'completed',
                'message' => __('Import completed.'),
                'total_rows' => $import->total_rows > 0 ? $import->total_rows : max(0, $finalProcessed),
                'processed_rows' => max(0, $finalProcessed),
                'last_row' => max((int) $import->last_row, $rowNumber - 1),
                'finished_at' => now(),
            ]);
        } catch (\Throwable $e) {
            $this->clearCaches();
            $this->appendLog($import, __('Import failed:') . ' ' . $e->getMessage());

            $import->update([
                'status' => 'failed',
                'message' => $e->getMessage(),
                'finished_at' => now(),
            ]);
        }
    }

    public function startBackgroundProcess(ProductImport $import): void
    {
        $php = $this->resolvePhpBinary();
        $artisan = base_path('artisan');
        $command = escapeshellarg($php) . ' ' . escapeshellarg($artisan) . ' products:import-supervisor ' . (int) $import->id;

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            pclose(popen('start /B "" ' . $command . ' > NUL 2>&1', 'r'));
        } else {
            exec($command . ' > /dev/null 2>&1 &');
        }
    }

    private function resolvePhpBinary(): string
    {
        $configured = env('IMPORT_PHP_BINARY');

        if ($configured && is_file($configured)) {
            return $configured;
        }

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $laragonRoot = getenv('LARAGON_ROOT') ?: 'C:\\laragon';
            $candidates = glob($laragonRoot . '\\bin\\php\\php-*\\php.exe') ?: [];
            rsort($candidates);

            foreach ($candidates as $candidate) {
                if (is_file($candidate)) {
                    return $candidate;
                }
            }
        }

        return PHP_BINARY;
    }

    private function processRow(ProductImport $import, array $line, int $rowNumber): void
    {
        $sku = trim($this->column($line, 0));

        if ($sku === '') {
            $this->registerSkip($import, $rowNumber, __('Missing SKU, row skipped.'));
            $this->touchProgress($import, $rowNumber);

            return;
        }

        if ($this->skuExists($sku)) {
            $this->registerSkip($import, $rowNumber, __('Duplicate Product Code!'));
            $this->touchProgress($import, $rowNumber);

            return;
        }

        try {
            $product = new Product;
            $product->fill($this->buildInput($line));
            $product->save();
            $this->rememberSku($sku);
            $import->imported_count++;
            $this->appendLog($import, __('Row') . ' ' . $rowNumber . ': ' . __('Imported') . ' ' . $sku);
        } catch (\Throwable $e) {
            $import->error_count++;
            $this->appendLog($import, __('Row') . ' ' . $rowNumber . ': ' . $e->getMessage());
        }

        if (! $this->skipImages && $rowNumber % 10 === 0) {
            gc_collect_cycles();
        }

        $this->touchProgress($import, $rowNumber);
    }

    private function registerSkip(ProductImport $import, int $rowNumber, string $reason): void
    {
        $import->skipped_count++;
        $this->appendLog($import, __('Row') . ' ' . $rowNumber . ': ' . $reason);
    }

    private function touchProgress(ProductImport $import, int $rowNumber): void
    {
        $import->processed_rows = $rowNumber - 1;
        $import->last_row = $rowNumber;

        $isFinalRow = $import->total_rows > 0 && $rowNumber === (int) $import->total_rows + 1;

        if ($rowNumber % $this->progressEvery !== 0 && ! $isFinalRow) {
            $import->save();

            return;
        }

        if ($import->total_rows > 0) {
            $import->message = __('Processed') . ' ' . number_format($import->processed_rows)
                . ' / ' . number_format($import->total_rows)
                . ' — ' . number_format($import->imported_count) . ' ' . __('imported');
        } else {
            $import->message = __('Processed') . ' ' . number_format($import->processed_rows)
                . ' — ' . number_format($import->imported_count) . ' ' . __('imported');
        }

        $import->save();
    }

    private function appendLog(ProductImport $import, string $line): void
    {
        $log = trim(($import->log ?? '') . "\n" . $this->safeUtf8($line));
        $lines = array_slice(array_filter(explode("\n", $log)), -300);
        $import->log = implode("\n", $lines);
    }

    private function initializeCaches(): void
    {
        $sign = Currency::where('is_default', '=', 1)->first();
        $this->currencyValue = (float) ($sign->value ?? 1);

        if ($this->currencyValue <= 0) {
            $this->currencyValue = 1;
        }

        $this->brandCache = Brand::pluck('id', 'name')
            ->mapWithKeys(fn ($id, $name) => [strtolower($name) => $id])
            ->all();
        $this->categoryCache = Category::pluck('id', 'name')
            ->mapWithKeys(fn ($id, $name) => [strtolower($name) => $id])
            ->all();
        $this->subcategoryCache = Subcategory::select('id', 'category_id', 'name')
            ->get()
            ->mapWithKeys(fn ($subcategory) => [
                $subcategory->category_id . ':' . strtolower($subcategory->name) => $subcategory->id,
            ])
            ->all();
        $this->childcategoryCache = Childcategory::select('id', 'subcategory_id', 'name')
            ->get()
            ->mapWithKeys(fn ($childcategory) => [
                $childcategory->subcategory_id . ':' . strtolower($childcategory->name) => $childcategory->id,
            ])
            ->all();
        // Session-only cache; existing SKUs are checked per row via the database.
        $this->skuCache = [];
    }

    private function skuExists(string $sku): bool
    {
        $skuKey = strtolower($sku);

        if (isset($this->skuCache[$skuKey])) {
            return true;
        }

        if (Product::whereRaw('LOWER(sku) = ?', [$skuKey])->exists()) {
            $this->skuCache[$skuKey] = true;

            return true;
        }

        return false;
    }

    private function rememberSku(string $sku): void
    {
        $this->skuCache[strtolower($sku)] = true;
    }

    private function clearCaches(): void
    {
        $this->brandCache = [];
        $this->categoryCache = [];
        $this->subcategoryCache = [];
        $this->childcategoryCache = [];
        $this->skuCache = [];
    }

    private function countDataRows(string $path): int
    {
        if (filesize($path) > self::LARGE_FILE_BYTES) {
            return 0;
        }

        $handle = $this->openImportCsv($path);
        $count = 0;
        $row = 0;

        while (fgetcsv($handle) !== false) {
            $row++;
            if ($row > 1) {
                $count++;
            }
        }

        fclose($handle);

        return $count;
    }

    public function openImportCsv(string $path)
    {
        if (filesize($path) > self::LARGE_FILE_BYTES) {
            return $this->openImportCsvStream($path);
        }

        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new \RuntimeException(__('Could not read the uploaded CSV file.'));
        }

        if (str_starts_with($contents, "\xEF\xBB\xBF")) {
            $contents = substr($contents, 3);
        }

        if (! mb_check_encoding($contents, 'UTF-8')) {
            $converted = @mb_convert_encoding($contents, 'UTF-8', 'Windows-1254, ISO-8859-9, CP1254, UTF-8');

            if ($converted !== false) {
                $contents = $converted;
            }
        }

        $handle = fopen('php://memory', 'r+');
        fwrite($handle, $contents);
        rewind($handle);

        return $handle;
    }

    private function openImportCsvStream(string $path)
    {
        $handle = fopen($path, 'rb');

        if ($handle === false) {
            throw new \RuntimeException(__('Could not read the uploaded CSV file.'));
        }

        $bom = fread($handle, 3);

        if ($bom !== "\xEF\xBB\xBF") {
            rewind($handle);
        }

        return $handle;
    }

    private function safeUtf8(string $value): string
    {
        if ($value === '') {
            return $value;
        }

        if (mb_check_encoding($value, 'UTF-8')) {
            return $value;
        }

        $converted = @mb_convert_encoding($value, 'UTF-8', 'Windows-1254, ISO-8859-9, CP1254, UTF-8');

        return $converted !== false ? $converted : iconv('UTF-8', 'UTF-8//IGNORE', $value);
    }

    private function column(array $line, int $index, string $default = ''): string
    {
        $value = isset($line[$index]) ? trim((string) $line[$index]) : $default;

        return $this->safeUtf8($value);
    }

    private function slug(string $value): string
    {
        $slug = Str::slug($value, '-');

        return $slug !== '' ? $slug : Str::random(8);
    }

    private function resolveBrandId(array $line): ?int
    {
        $brandName = $this->column($line, 21);

        if ($brandName === '') {
            $brandName = $this->column($line, 1);
            if (strtolower($brandName) === 'anasayfa') {
                $brandName = $this->column($line, 2);
            }
        }

        if ($brandName === '') {
            return null;
        }

        $slug = $this->slug($brandName);
        $cacheKey = strtolower($brandName);

        if (isset($this->brandCache[$cacheKey])) {
            return $this->brandCache[$cacheKey];
        }

        $brand = Brand::where(function ($query) use ($brandName, $slug) {
            $query->where(DB::raw('lower(name)'), strtolower($brandName))
                ->orWhere(DB::raw('lower(slug)'), strtolower($slug));
        })->first();

        if ($brand) {
            $this->brandCache[$cacheKey] = $brand->id;

            return $brand->id;
        }

        $brandId = Brand::create([
            'name' => $brandName,
            'slug' => $slug,
        ])->id;

        $this->brandCache[$cacheKey] = $brandId;

        return $brandId;
    }

    private function resolveCategoryId(array $line): int
    {
        $categoryName = $this->column($line, 1);

        if ($categoryName === '' || strtolower($categoryName) === 'anasayfa') {
            $categoryName = $this->column($line, 21) ?: $this->column($line, 2) ?: 'General';
        }

        $cacheKey = strtolower($categoryName);

        if (isset($this->categoryCache[$cacheKey])) {
            return $this->categoryCache[$cacheKey];
        }

        $category = Category::where(DB::raw('lower(name)'), strtolower($categoryName))->first();

        if ($category) {
            $this->categoryCache[$cacheKey] = $category->id;

            return $category->id;
        }

        $categoryId = Category::create([
            'name' => $categoryName,
            'slug' => $this->slug($categoryName),
        ])->id;

        $this->categoryCache[$cacheKey] = $categoryId;

        return $categoryId;
    }

    private function resolveSubcategoryId(int $categoryId, array $line): ?int
    {
        $subcategoryName = $this->column($line, 2);

        if ($subcategoryName === '') {
            return null;
        }

        $cacheKey = $categoryId . ':' . strtolower($subcategoryName);

        if (isset($this->subcategoryCache[$cacheKey])) {
            return $this->subcategoryCache[$cacheKey];
        }

        $subcategory = Subcategory::where('category_id', $categoryId)
            ->where(DB::raw('lower(name)'), strtolower($subcategoryName))
            ->first();

        if ($subcategory) {
            $this->subcategoryCache[$cacheKey] = $subcategory->id;

            return $subcategory->id;
        }

        $subcategoryId = Subcategory::create([
            'category_id' => $categoryId,
            'name' => $subcategoryName,
            'slug' => $this->slug($subcategoryName),
        ])->id;

        $this->subcategoryCache[$cacheKey] = $subcategoryId;

        return $subcategoryId;
    }

    private function resolveChildcategoryId(?int $subcategoryId, array $line): ?int
    {
        $childcategoryName = $this->column($line, 3);

        if ($childcategoryName === '' || $subcategoryId === null) {
            return null;
        }

        $cacheKey = $subcategoryId . ':' . strtolower($childcategoryName);

        if (isset($this->childcategoryCache[$cacheKey])) {
            return $this->childcategoryCache[$cacheKey];
        }

        $childcategory = Childcategory::where('subcategory_id', $subcategoryId)
            ->where(DB::raw('lower(name)'), strtolower($childcategoryName))
            ->first();

        if ($childcategory) {
            $this->childcategoryCache[$cacheKey] = $childcategory->id;

            return $childcategory->id;
        }

        $childcategoryId = Childcategory::create([
            'subcategory_id' => $subcategoryId,
            'name' => $childcategoryName,
            'slug' => $this->slug($childcategoryName),
        ])->id;

        $this->childcategoryCache[$cacheKey] = $childcategoryId;

        return $childcategoryId;
    }

    private function resolvePhotos(string $imageUrl): array
    {
        $placeholder = [
            'photo' => 'noimage.png',
            'thumbnail' => 'noimage.png',
        ];

        if ($this->skipImages || $imageUrl === '' || ! filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            return $placeholder;
        }

        try {
            $request = Http::timeout(15)
                ->withHeaders(['User-Agent' => 'Mozilla/5.0 (compatible; IndustrialMac/1.0)']);

            if (app()->environment('local')) {
                $request = $request->withoutVerifying();
            }

            $response = $request->get($imageUrl);

            if (! $response->successful()) {
                return $placeholder;
            }

            $contentType = strtolower((string) $response->header('Content-Type'));

            if ($contentType !== '' && ! str_contains($contentType, 'image')) {
                return $placeholder;
            }

            $extension = 'jpg';
            if (str_contains($contentType, 'png')) {
                $extension = 'png';
            } elseif (str_contains($contentType, 'webp')) {
                $extension = 'webp';
            } elseif (str_contains($contentType, 'gif')) {
                $extension = 'gif';
            }

            $fphoto = time() . Str::random(8) . '.' . $extension;
            $thumbnail = time() . Str::random(8) . '.jpg';
            $productPath = public_path('assets/images/products/' . $fphoto);
            $thumbnailPath = public_path('assets/images/thumbnails/' . $thumbnail);

            file_put_contents($productPath, $response->body());
            unset($response);

            try {
                $image = Image::make($productPath);
                $image->resize(1000, 1000, function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })->encode($extension === 'png' ? 'png' : 'jpg', 90)->save($productPath);
                $image->destroy();
                unset($image);

                // Larger, higher-quality thumbs for retina / wide product cards
                $thumbnailImage = Image::make($productPath);
                $thumbnailImage->fit(700, 700)->encode('jpg', 90)->save($thumbnailPath);
                $thumbnailImage->destroy();
                unset($thumbnailImage);
            } catch (\Throwable $imageError) {
                copy($productPath, $thumbnailPath);
            }

            return [
                'photo' => $fphoto,
                'thumbnail' => $thumbnail,
            ];
        } catch (\Throwable $e) {
            return $placeholder;
        }
    }

    private function buildInput(array $line): array
    {
        $categoryId = $this->resolveCategoryId($line);
        $subcategoryId = $this->resolveSubcategoryId($categoryId, $line);
        $childcategoryId = $this->resolveChildcategoryId($subcategoryId, $line);
        $photos = $this->resolvePhotos($this->column($line, 5));

        $name = $this->column($line, 4);
        if ($name === '') {
            $name = $this->column($line, 0);
        }

        $sku = $this->column($line, 0);
        $price = is_numeric($this->column($line, 7)) ? (float) $this->column($line, 7) : 0;
        $previousPriceRaw = $this->column($line, 8);
        $previousPrice = $previousPriceRaw !== '' && is_numeric($previousPriceRaw) ? (float) $previousPriceRaw : null;

        $affiliateLink = $this->column($line, 20);
        if ($affiliateLink === '') {
            $affiliateLink = $this->column($line, 22);
        }

        $productType = strtolower($this->column($line, 19, 'normal'));
        if (! in_array($productType, ['normal', 'affiliate'], true)) {
            $productType = 'normal';
        }

        return [
            'type' => 'Physical',
            'sku' => $sku,
            'user_id' => 0,
            'brand_id' => $this->resolveBrandId($line),
            'category_id' => $categoryId,
            'subcategory_id' => $subcategoryId,
            'childcategory_id' => $childcategoryId,
            'photo' => $photos['photo'],
            'thumbnail' => $photos['thumbnail'],
            'name' => $name,
            'details' => $this->column($line, 6),
            'color' => $this->column($line, 13),
            'price' => $price / $this->currencyValue,
            'previous_price' => $previousPrice !== null ? $previousPrice / $this->currencyValue : null,
            'stock' => is_numeric($this->column($line, 9)) ? (int) $this->column($line, 9) : 0,
            'size' => $this->column($line, 10),
            'size_qty' => is_numeric($this->column($line, 11)) ? (int) $this->column($line, 11) : 0,
            'size_price' => $this->column($line, 12),
            'youtube' => $this->column($line, 15),
            'policy' => $this->column($line, 16),
            'meta_tag' => $this->column($line, 17),
            'meta_description' => $this->column($line, 18),
            'tags' => $this->column($line, 14),
            'product_type' => $productType,
            'affiliate_link' => $affiliateLink,
            'latest' => 1,
            'status' => 1,
            'slug' => Str::slug($name, '-') . '-' . strtolower($sku),
        ];
    }
}
