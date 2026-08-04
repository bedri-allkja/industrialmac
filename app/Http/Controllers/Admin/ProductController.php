<?php

namespace App\Http\Controllers\Admin;

use App\Models\Attribute;
use App\Models\AttributeOption;
use App\Models\Category;
use App\Models\Childcategory;
use App\Models\Currency;
use App\Models\Gallery;
use App\Models\Product;
use App\Models\Brand;
use App\Models\Subcategory;
use App\Models\ProductImport;
use App\Services\ProductImportService;
use Datatables;use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Image;
use Validator;

class ProductController extends AdminBaseController
{
    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
    
    //*** JSON Request
    public function datatables(Request $request)
    {
        $query = Product::query()
            ->with(['brand:id,name', 'user:id,shop_name'])
            ->where('product_type', 'normal');

        if ($request->type === 'deactive') {
            $query->where('status', 0);
        }

        return $this->productListDatatable($query->latest('id'));
    }

    //*** JSON Request
    public function catalogdatatables()
    {
        $query = Product::query()
            ->with(['brand:id,name', 'user:id,shop_name'])
            ->where('is_catalog', 1)
            ->orderBy('id', 'desc');

        return $this->productListDatatable($query, false);
    }

    private function productListDatatable($query, bool $includeCatalogActions = true)
    {
        return Datatables::of($query)
            ->filterColumn('name', function ($query, $keyword) {
                $query->where(function ($q) use ($keyword) {
                    $q->where('products.name', 'like', "%{$keyword}%")
                        ->orWhere('products.sku', 'like', "%{$keyword}%");
                });
            })
            ->filterColumn('brand', function ($query, $keyword) {
                $query->whereHas('brand', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->editColumn('name', function (Product $data) {
                $name = mb_strlen($data->name, 'UTF-8') > 50 ? mb_substr($data->name, 0, 50, 'UTF-8') . '...' : $data->name;
                $id = '<small>' . __("ID") . ': <a href="' . route('front.product', $data->slug) . '" target="_blank">' . sprintf("%'.08d", $data->id) . '</a></small>';
                $id3 = $data->type == 'Physical' ? '<small class="ml-2"> ' . __("SKU") . ': <a href="' . route('front.product', $data->slug) . '" target="_blank">' . $data->sku . '</a></small>' : '';
                return $name . '<br>' . $id . $id3 . $data->checkVendor();
            })
            ->editColumn('price', function (Product $data) {
                $price = $data->price * $this->curr->value;
                return \PriceHelper::showAdminCurrencyPrice($price);
            })
            ->editColumn('photo', function (Product $data) {
                $photo = $data->photo ? asset('assets/images/products/' . $data->photo) : asset('assets/images/noimage.png');
                return '<img src="' . $photo . '" alt="Image" class="img-thumbnail" style="width:80px">';
            })
            ->editColumn('stock', function (Product $data) {
                $stck = (string) $data->stock;
                if ($stck == "0") {
                    return __("Out Of Stock");
                } elseif ($stck == null) {
                    return __("Unlimited");
                } else {
                    return $data->stock;
                }

            })
            ->addColumn('status', function (Product $data) {
                $class = $data->status == 1 ? 'drop-success' : 'drop-danger';
                $s = $data->status == 1 ? 'selected' : '';
                $ns = $data->status == 0 ? 'selected' : '';
                return '<div class="action-list"><select class="process select droplinks ' . $class . '"><option data-val="1" value="' . route('admin-prod-status', ['id1' => $data->id, 'id2' => 1]) . '" ' . $s . '>' . __("Activated") . '</option><option data-val="0" value="' . route('admin-prod-status', ['id1' => $data->id, 'id2' => 0]) . '" ' . $ns . '>' . __("Deactivated") . '</option>/select></div>';
            })
            ->addColumn('brand', function (Product $data) {
                return $data->brand ? $data->brand->name : __('No Brand');
            })
            ->addColumn('action', function (Product $data) use ($includeCatalogActions) {
                if ($includeCatalogActions) {
                    $catalog = $data->type == 'Physical' ? ($data->is_catalog == 1 ? '<a href="javascript:;" data-href="' . route('admin-prod-catalog', ['id1' => $data->id, 'id2' => 0]) . '" data-toggle="modal" data-target="#catalog-modal" class="delete"><i class="fas fa-trash-alt"></i> ' . __("Remove Catalog") . '</a>' : '<a href="javascript:;" data-href="' . route('admin-prod-catalog', ['id1' => $data->id, 'id2' => 1]) . '" data-toggle="modal" data-target="#catalog-modal"> <i class="fas fa-plus"></i> ' . __("Add To Catalog") . '</a>') : '';
                    return '<div class="godropdown"><button class="go-dropdown-toggle"> ' . __("Actions") . '<i class="fas fa-chevron-down"></i></button><div class="action-list"><a href="' . route('admin-prod-edit', $data->id) . '"> <i class="fas fa-edit"></i> ' . __("Edit") . '</a><a href="javascript" class="set-gallery" data-toggle="modal" data-target="#setgallery"><input type="hidden" value="' . $data->id . '"><i class="fas fa-eye"></i> ' . __("View Gallery") . '</a>' . $catalog . '<a data-href="' . route('admin-prod-feature', $data->id) . '" class="feature" data-toggle="modal" data-target="#modal2"> <i class="fas fa-star"></i> ' . __("Highlight") . '</a><a href="javascript:;" data-href="' . route('admin-prod-delete', $data->id) . '" data-toggle="modal" data-target="#confirm-delete" class="delete"><i class="fas fa-trash-alt"></i> ' . __("Delete") . '</a></div></div>';
                }

                return '<div class="godropdown"><button class="go-dropdown-toggle">  ' . __("Actions") . '<i class="fas fa-chevron-down"></i></button><div class="action-list"><a href="' . route('admin-prod-edit', $data->id) . '"> <i class="fas fa-edit"></i> ' . __("Edit") . '</a><a href="javascript" class="set-gallery" data-toggle="modal" data-target="#setgallery"><input type="hidden" value="' . $data->id . '"><i class="fas fa-eye"></i> ' . __("View Gallery") . '</a><a data-href="' . route('admin-prod-feature', $data->id) . '" class="feature" data-toggle="modal" data-target="#modal2"> <i class="fas fa-star"></i> ' . __("Highlight") . '</a><a href="javascript:;" data-href="' . route('admin-prod-catalog', ['id1' => $data->id, 'id2' => 0]) . '" data-toggle="modal" data-target="#catalog-modal"><i class="fas fa-trash-alt"></i> ' . __("Remove Catalog") . '</a></div></div>';
            })
            ->rawColumns(['name', 'status', 'action', 'photo'])
            ->toJson();
    }

    public function productscatalog()
    {
        return view('admin.product.catalog');
    }

    public function index()
    {
        return view('admin.product.index');
    }

    public function types()
    {
        return view('admin.product.types');
    }

    public function deactive()
    {
        return view('admin.product.deactive');
    }

    public function productsettings()
    {
        return view('admin.product.settings');
    }

    //*** GET Request
    public function create($slug)
    {
        $cats = Category::all();
        $brands = Brand::all();
        $sign = $this->curr;
        if ($slug == 'physical') {
            return view('admin.product.create.physical', compact('cats', 'brands', 'sign'));
        } else if ($slug == 'digital') {
            return view('admin.product.create.digital', compact('cats', 'brands', 'sign'));
        } else if (($slug == 'license')) {
            return view('admin.product.create.license', compact('cats', 'brands', 'sign'));
        } else if (($slug == 'listing')) {
            return view('admin.product.create.listing', compact('cats', 'brands', 'sign'));

        }
    }

    //*** GET Request
    public function status($id1, $id2)
    {
        $data = Product::findOrFail($id1);
        $data->status = $id2;
        $data->update();
        //--- Redirect Section
        $msg = __('Status Updated Successfully.');
        return response()->json($msg);
        //--- Redirect Section Ends
    }

    //*** POST Request
    public function uploadUpdate(Request $request, $id)
    {

        //--- Validation Section
        $rules = [
            'image' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }

        $data = Product::findOrFail($id);

        //--- Validation Section Ends
        $image = $request->image;
        list($type, $image) = explode(';', $image);
        list(, $image) = explode(',', $image);
        $image = base64_decode($image);
        $image_name = time() . Str::random(8) . '.png';
        $path = 'assets/images/products/' . $image_name;
        file_put_contents($path, $image);
        if ($data->photo != null) {
            if (file_exists(public_path() . '/assets/images/products/' . $data->photo)) {
                unlink(public_path() . '/assets/images/products/' . $data->photo);
            }
        }
        $input['photo'] = $image_name;
        $data->update($input);
        if ($data->thumbnail != null) {
            if (file_exists(public_path() . '/assets/images/thumbnails/' . $data->thumbnail)) {
                unlink(public_path() . '/assets/images/thumbnails/' . $data->thumbnail);
            }
        }

        $img = Image::make('assets/images/products/' . $data->photo)->resize(285, 285);
        $thumbnail = time() . Str::random(8) . '.jpg';
        $img->save('assets/images/thumbnails/' . $thumbnail);
        $data->thumbnail = $thumbnail;
        $data->update();
        return response()->json(['status' => true, 'file_name' => $image_name]);
    }

    //*** POST Request
    public function store(Request $request)
    {
        //--- Validation Section
        $rules = [
            'photo' => 'required',
            'brand_id' => 'required|exists:brands,id',
            'file' => 'mimes:zip',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }
        //--- Validation Section Ends

        //--- Logic Section
        $data = new Product;
        $sign = $this->curr;
        $input = $request->all();

        // Check File
        if ($file = $request->file('file')) {
            $name = time() . \Str::random(8) . str_replace(' ', '', $file->getClientOriginalExtension());
            $file->move('assets/files', $name);
            $input['file'] = $name;
        }

        $image = $request->photo;
        list($type, $image) = explode(';', $image);
        list(, $image) = explode(',', $image);
        $image = base64_decode($image);
        $image_name = time() . Str::random(8) . '.png';
        $path = 'assets/images/products/' . $image_name;
        file_put_contents($path, $image);
        $input['photo'] = $image_name;

        if ($request->type == "Physical" || $request->type == "Listing") {
            $rules = ['sku' => 'min:8|unique:products'];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
            }

            if ($request->product_condition_check == "") {
                $input['product_condition'] = 0;
            }

            if ($request->preordered_check == "") {
                $input['preordered'] = 0;
            }

            if ($request->minimum_qty_check == "") {
                $input['minimum_qty'] = null;
            }

            if ($request->shipping_time_check == "") {
                $input['ship'] = null;
            }

            if (empty($request->stock_check)) {
                $input['stock_check'] = 0;
                $input['size'] = null;
                $input['size_qty'] = null;
                $input['size_price'] = null;
            } else {
                if (in_array(null, $request->size) || in_array(null, $request->size_qty) || in_array(null, $request->size_price)) {
                    $input['stock_check'] = 0;
                    $input['size'] = null;
                    $input['size_qty'] = null;
                    $input['size_price'] = null;
                } else {
                    $input['stock_check'] = 1;
                    $input['size'] = implode(',', $request->size);
                    $input['size_qty'] = implode(',', $request->size_qty);
                    $size_prices = $request->size_price;
                    $s_price = array();
                    foreach ($size_prices as $key => $sPrice) {
                        $s_price[$key] = $sPrice / $sign->value;
                    }
                    $input['size_price'] = implode(',', $s_price);
                }
            }

            if (empty($request->color_check)) {
                $input['color_all'] = null;
                $input['color_price'] = null;
            } else {
              
                $input['color_all'] = implode(',', $request->color_all);
            }

            if (empty($request->whole_check)) {
                $input['whole_sell_qty'] = null;
                $input['whole_sell_discount'] = null;
            } else {
                if (in_array(null, $request->whole_sell_qty) || in_array(null, $request->whole_sell_discount)) {
                    $input['whole_sell_qty'] = null;
                    $input['whole_sell_discount'] = null;
                } else {
                    $input['whole_sell_qty'] = implode(',', $request->whole_sell_qty);
                    $input['whole_sell_discount'] = implode(',', $request->whole_sell_discount);
                }
            }

            if ($request->mesasure_check == "") {
                $input['measure'] = null;
            }

        }

        if (empty($request->seo_check)) {
            $input['meta_tag'] = null;
            $input['meta_description'] = null;
        } else {
            if (!empty($request->meta_tag)) {
                $input['meta_tag'] = implode(',', $request->meta_tag);
            }
        }

        if ($request->type == "License") {

            if (in_array(null, $request->license) || in_array(null, $request->license_qty)) {
                $input['license'] = null;
                $input['license_qty'] = null;
            } else {
                $input['license'] = implode(',,', $request->license);
                $input['license_qty'] = implode(',', $request->license_qty);
            }

        }

        if (in_array(null, $request->features) || in_array(null, $request->colors)) {
            $input['features'] = null;
            $input['colors'] = null;
        } else {
            $input['features'] = implode(',', str_replace(',', ' ', $request->features));
            $input['colors'] = implode(',', str_replace(',', ' ', $request->colors));
        }

        if (!empty($request->tags)) {
            $input['tags'] = implode(',', $request->tags);
        }

        $input['price'] = ($input['price'] / $sign->value);
        $input['previous_price'] = ($input['previous_price'] / $sign->value);
        if ($request->cross_products) {
            $input['cross_products'] = implode(',', $request->cross_products);
        }

        $attrArr = [];
        if (!empty($request->category_id)) {
            $catAttrs = Attribute::where('attributable_id', $request->category_id)->where('attributable_type', 'App\Models\Category')->get();
            if (!empty($catAttrs)) {
                foreach ($catAttrs as $key => $catAttr) {
                    $in_name = $catAttr->input_name;
                    if ($request->has("$in_name")) {
                        $attrArr["$in_name"]["values"] = $request["$in_name"];
                        foreach ($request["$in_name" . "_price"] as $aprice) {
                            $ttt["$in_name" . "_price"][] = $aprice / $sign->value;
                        }
                        $attrArr["$in_name"]["prices"] = $ttt["$in_name" . "_price"];
                        if ($catAttr->details_status) {
                            $attrArr["$in_name"]["details_status"] = 1;
                        } else {
                            $attrArr["$in_name"]["details_status"] = 0;
                        }
                    }
                }
            }
        }

        if (!empty($request->subcategory_id)) {
            $subAttrs = Attribute::where('attributable_id', $request->subcategory_id)->where('attributable_type', 'App\Models\Subcategory')->get();
            if (!empty($subAttrs)) {
                foreach ($subAttrs as $key => $subAttr) {
                    $in_name = $subAttr->input_name;
                    if ($request->has("$in_name")) {
                        $attrArr["$in_name"]["values"] = $request["$in_name"];
                        foreach ($request["$in_name" . "_price"] as $aprice) {
                            $ttt["$in_name" . "_price"][] = $aprice / $sign->value;
                        }
                        $attrArr["$in_name"]["prices"] = $ttt["$in_name" . "_price"];
                        if ($subAttr->details_status) {
                            $attrArr["$in_name"]["details_status"] = 1;
                        } else {
                            $attrArr["$in_name"]["details_status"] = 0;
                        }
                    }
                }
            }
        }

        if (!empty($request->childcategory_id)) {
            $childAttrs = Attribute::where('attributable_id', $request->childcategory_id)->where('attributable_type', 'App\Models\Childcategory')->get();
            if (!empty($childAttrs)) {
                foreach ($childAttrs as $key => $childAttr) {
                    $in_name = $childAttr->input_name;
                    if ($request->has("$in_name")) {
                        $attrArr["$in_name"]["values"] = $request["$in_name"];
                        foreach ($request["$in_name" . "_price"] as $aprice) {
                            $ttt["$in_name" . "_price"][] = $aprice / $sign->value;
                        }
                        $attrArr["$in_name"]["prices"] = $ttt["$in_name" . "_price"];
                        if ($childAttr->details_status) {
                            $attrArr["$in_name"]["details_status"] = 1;
                        } else {
                            $attrArr["$in_name"]["details_status"] = 0;
                        }
                    }
                }
            }
        }

        if (empty($attrArr)) {
            $input['attributes'] = null;
        } else {
            $jsonAttr = json_encode($attrArr);
            $input['attributes'] = $jsonAttr;
        }

        $input['brand_id'] = $request->brand_id;

        // dd($input);

        // Save Data
        $data->fill($input)->save();

        // Set SLug
        $prod = Product::find($data->id);
        if ($prod->type != 'Physical' || $request->type != "Listing") {
            $prod->slug = product_make_slug($data->name, Str::random(3) . $data->id . Str::random(3));
        } else {
            $prod->slug = product_make_slug($data->name, (string) $data->sku);
        }

        // Set Thumbnail
        $img = Image::make('assets/images/products/' . $prod->photo)->resize(285, 285);
        $thumbnail = time() . Str::random(8) . '.jpg';
        $img->save('assets/images/thumbnails/' . $thumbnail);
        $prod->thumbnail = $thumbnail;
        $prod->update();

        // Add To Gallery If any
        $lastid = $data->id;
        if ($files = $request->file('gallery')) {
            foreach ($files as $key => $file) {
                if (in_array($key, $request->galval)) {
                    $gallery = new Gallery;
                    $name = time() . \Str::random(8) . str_replace(' ', '', $file->getClientOriginalExtension());
                    $file->move('assets/images/galleries', $name);
                    $gallery['photo'] = $name;
                    $gallery['product_id'] = $lastid;
                    $gallery->save();
                }
            }
        }
        //logic Section Ends

        //--- Redirect Section
        $msg = __("New Product Added Successfully.") . '<a href="' . route('admin-prod-index') . '">' . __("View Product Lists.") . '</a>';
        return response()->json($msg);
        //--- Redirect Section Ends
    }

    //*** GET Request
    public function import()
    {
        $cats = Category::all();
        $brands = Brand::all();
        $sign = $this->curr;
        $activeImport = ProductImport::whereIn('status', ['pending', 'running'])
            ->latest('id')
            ->first();

        return view('admin.product.productcsv', compact('cats', 'brands', 'sign', 'activeImport'));
    }

    public function importFiles()
    {
        $files = [];

        foreach ($this->importStorageFiles() as $path) {
            $files[] = [
                'name' => basename($path),
                'size' => filesize($path),
                'size_human' => $this->formatImportBytes((int) filesize($path)),
                'modified' => date('Y-m-d H:i:s', filemtime($path)),
            ];
        }

        usort($files, fn ($a, $b) => strcmp($b['modified'], $a['modified']));

        return response()->json([
            'directory' => $this->importStorageDir(),
            'files' => $files,
            'upload_limit' => ini_get('upload_max_filesize'),
            'post_limit' => ini_get('post_max_size'),
        ]);
    }

    public function importStartFile(Request $request, ProductImportService $importService)
    {
        $validator = Validator::make($request->all(), [
            'filename' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->getMessageBag()->toArray()]);
        }

        $path = $this->resolveImportStoragePath($request->input('filename'));

        if ($path === null) {
            return response()->json([
                'message' => __('File not found in the import folder.'),
            ], 404);
        }

        $import = $this->createAndQueueImport(
            $importService,
            $path,
            basename($path),
            $request->boolean('skip_images', true),
            true
        );

        return response()->json([
            'import_id' => $import->id,
            'background' => true,
            'message' => __('Import started from server file. Track progress below.'),
            'status' => $import->fresh()->toStatusArray(),
        ], 200, [], JSON_INVALID_UTF8_SUBSTITUTE);
    }

    public function importStatus($id = null)
    {
        $import = $id
            ? ProductImport::findOrFail($id)
            : ProductImport::latest('id')->first();

        if (! $import) {
            return response()->json(['message' => __('No import found.')], 404);
        }

        return response()->json($import->fresh()->toStatusArray(), 200, [], JSON_INVALID_UTF8_SUBSTITUTE);
    }

    //*** POST Request
    public function importSubmit(Request $request, ProductImportService $importService)
    {
        @set_time_limit(0);

        if (! $request->hasFile('csvfile')) {
            return response()->json([
                'message' => __('No file received. Your CSV may exceed the PHP upload limit (:limit). Copy it to the server import folder instead.', [
                    'limit' => ini_get('upload_max_filesize'),
                ]),
            ], 422);
        }

        $rules = [
            'csvfile' => 'required|mimes:csv,txt',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->getMessageBag()->toArray()]);
        }

        $uploadedFile = $request->file('csvfile');
        $originalName = $uploadedFile->getClientOriginalName();
        $storageDir = $this->importStorageDir();

        $storedName = time() . '-' . Str::random(8) . '.csv';
        $storedPath = $storageDir . DIRECTORY_SEPARATOR . $storedName;
        $uploadedFile->move($storageDir, $storedName);

        $background = $request->boolean('background');
        $skipImages = $request->boolean('skip_images');

        if ($background) {
            $import = $this->createAndQueueImport($importService, $storedPath, $originalName, $skipImages, true);

            return response()->json([
                'import_id' => $import->id,
                'background' => true,
                'message' => __('Import started in the background. Track progress below.'),
                'status' => $import->fresh()->toStatusArray(),
            ], 200, [], JSON_INVALID_UTF8_SUBSTITUTE);
        }

        $import = ProductImport::create([
            'admin_id' => Auth::guard('admin')->id(),
            'original_name' => $originalName,
            'file_path' => $storedPath,
            'status' => 'pending',
            'skip_images' => $skipImages,
            'background' => false,
            'message' => __('Import queued.'),
        ]);

        $importService->run($import);
        $import->refresh();

        $message = __('Bulk Product File Imported Successfully.')
            . ' (' . $import->imported_count . ' ' . __('products') . ', ' . ($import->elapsedSeconds() ?? 0) . 's)';

        if ($import->status === 'failed') {
            return response()->json([
                'import_id' => $import->id,
                'message' => $import->message,
                'status' => $import->toStatusArray(),
            ], 422, [], JSON_INVALID_UTF8_SUBSTITUTE);
        }

        return response()->json([
            'import_id' => $import->id,
            'background' => false,
            'message' => $message,
            'imported' => $import->imported_count,
            'elapsed' => $import->elapsedSeconds(),
            'status' => $import->toStatusArray(),
        ], 200, [], JSON_INVALID_UTF8_SUBSTITUTE);
    }

    private function createAndQueueImport(
        ProductImportService $importService,
        string $storedPath,
        string $originalName,
        bool $skipImages,
        bool $background
    ): ProductImport {
        $import = ProductImport::create([
            'admin_id' => Auth::guard('admin')->id(),
            'original_name' => $originalName,
            'file_path' => $storedPath,
            'status' => 'pending',
            'skip_images' => $skipImages,
            'background' => $background,
            'message' => __('Import queued.'),
        ]);

        $importService->startBackgroundProcess($import);

        return $import;
    }

    private function importStorageDir(): string
    {
        $dir = storage_path('app/product-imports');

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        return $dir;
    }

    private function importStorageFiles(): array
    {
        $files = array_merge(
            glob($this->importStorageDir() . DIRECTORY_SEPARATOR . '*.csv') ?: [],
            glob($this->importStorageDir() . DIRECTORY_SEPARATOR . '*.txt') ?: []
        );

        return array_values(array_filter($files, 'is_file'));
    }

    private function resolveImportStoragePath(string $filename): ?string
    {
        $filename = basename(str_replace(['..', '\\', '/'], '', $filename));
        $path = $this->importStorageDir() . DIRECTORY_SEPARATOR . $filename;

        return is_file($path) ? $path : null;
    }

    private function formatImportBytes(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return round($bytes / 1073741824, 2) . ' GB';
        }

        if ($bytes >= 1048576) {
            return round($bytes / 1048576, 2) . ' MB';
        }

        if ($bytes >= 1024) {
            return round($bytes / 1024, 2) . ' KB';
        }

        return $bytes . ' B';
    }

    public function edit($id)
    {
        $cats = Category::all();
        $brands = Brand::all();
        $data = Product::findOrFail($id);
        $sign = $this->curr;

        if ($data->type == 'Digital') {
            return view('admin.product.edit.digital', compact('cats', 'data', 'brands', 'sign'));
        } elseif ($data->type == 'License') {
            return view('admin.product.edit.license', compact('cats', 'data', 'brands', 'sign'));
        } elseif ($data->type == 'Listing') {
            return view('admin.product.edit.listing', compact('cats', 'data', 'brands', 'sign'));
        } else {
            return view('admin.product.edit.physical', compact('cats', 'data', 'brands', 'sign'));
        }

    }

    //*** POST Request
    public function update(Request $request, $id)
    {
        // return $request;
        //--- Validation Section

        $rules = [
            'file' => 'mimes:zip',
            'brand_id' => 'required|exists:brands,id', // Add this rule
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
        }
        //--- Validation Section Ends

        //-- Logic Section
        $data = Product::findOrFail($id);
        $sign = $this->curr;
        $input = $request->all();

        //Check Types
        if ($request->type_check == 1) {
            $input['link'] = null;
        } else {
            if ($data->file != null) {
                if (file_exists(public_path() . '/assets/files/' . $data->file)) {
                    unlink(public_path() . '/assets/files/' . $data->file);
                }
            }
            $input['file'] = null;
        }

        // Check Physical
        if ($data->type == "Physical" || $data->type == "Listing") {
            //--- Validation Section
            $rules = ['sku' => 'min:8|unique:products,sku,' . $id];

            $validator = Validator::make($request->all(), $rules);

            if ($validator->fails()) {
                return response()->json(array('errors' => $validator->getMessageBag()->toArray()));
            }
            //--- Validation Section Ends

            // Check Condition
            if ($request->product_condition_check == "") {
                $input['product_condition'] = 0;
            }

            // Check Preorderd
            if ($request->preordered_check == "") {
                $input['preordered'] = 0;
            }

            // Check Minimum Qty
            if ($request->minimum_qty_check == "") {
                $input['minimum_qty'] = null;
            }

            // Check Shipping Time
            if ($request->shipping_time_check == "") {
                $input['ship'] = null;
            }

            // Check Size
            if (empty($request->stock_check)) {
                $input['stock_check'] = 0;
                $input['size'] = null;
                $input['size_qty'] = null;
                $input['size_price'] = null;
            } else {
                if (in_array(null, $request->size) || in_array(null, $request->size_qty) || in_array(null, $request->size_price)) {
                    $input['stock_check'] = 0;
                    $input['size'] = null;
                    $input['size_qty'] = null;
                    $input['size_price'] = null;
                } else {
                    $input['stock_check'] = 1;
                    $input['size'] = implode(',', $request->size);
                    $input['size_qty'] = implode(',', $request->size_qty);
                    $size_prices = $request->size_price;
                    $s_price = array();
                    foreach ($size_prices as $key => $sPrice) {
                        $s_price[$key] = $sPrice / $sign->value;
                    }
                    $input['size_price'] = implode(',', $s_price);
                }
            }

            if (empty($request->color_check)) {
                $input['color_all'] = null;
            } else {
                $input['color_all'] = implode(',', $request->color_all);
                // $color_prices = $request->color_price;
                // $c_price = array();
                // foreach ($color_prices as $key => $sPrice) {
                //     $c_price[$key] = $sPrice / $sign->value;
                // }
                // $input['color_price'] = implode(',', $c_price);
            }

            // Check Whole Sale
            if (empty($request->whole_check)) {
                $input['whole_sell_qty'] = null;
                $input['whole_sell_discount'] = null;
            } else {
                if (in_array(null, $request->whole_sell_qty) || in_array(null, $request->whole_sell_discount)) {
                    $input['whole_sell_qty'] = null;
                    $input['whole_sell_discount'] = null;
                } else {
                    $input['whole_sell_qty'] = implode(',', $request->whole_sell_qty);
                    $input['whole_sell_discount'] = implode(',', $request->whole_sell_discount);
                }
            }

            // Check Measure
            if ($request->measure_check == "") {
                $input['measure'] = null;
            }
        }

        // Check Seo
        if (empty($request->seo_check)) {
            $input['meta_tag'] = null;
            $input['meta_description'] = null;
        } else {
            if (!empty($request->meta_tag)) {
                $input['meta_tag'] = implode(',', $request->meta_tag);
            }
        }

        // Check License
        if ($data->type == "License") {

            if (!in_array(null, $request->license) && !in_array(null, $request->license_qty)) {
                $input['license'] = implode(',,', $request->license);
                $input['license_qty'] = implode(',', $request->license_qty);
            } else {
                if (in_array(null, $request->license) || in_array(null, $request->license_qty)) {
                    $input['license'] = null;
                    $input['license_qty'] = null;
                } else {
                    $license = explode(',,', $data->license);
                    $license_qty = explode(',', $data->license_qty);
                    $input['license'] = implode(',,', $license);
                    $input['license_qty'] = implode(',', $license_qty);
                }
            }

        }

        if (!in_array(null, $request->colors)) {
            $input['colors'] = implode(',', str_replace(',', ' ', $request->colors));
        } else {
            if (in_array(null, $request->features)) {
                $input['colors'] = null;
            } else {
                $colors = explode(',', $data->colors);
                $input['colors'] = implode(',', $colors);
            }
        }

        if (!in_array(null, $request->features) && !in_array(null, $request->colors)) {
            $input['features'] = implode(',', str_replace(',', ' ', $request->features));
        } else {
            if (in_array(null, $request->features) || in_array(null, $request->colors)) {
                $input['features'] = null;
            } else {
                $features = explode(',', $data->features);
                $input['features'] = implode(',', $features);
            }
        }

        if (!empty($request->tags)) {
            $input['tags'] = implode(',', $request->tags);
        }
        if (empty($request->tags)) {
            $input['tags'] = null;
        }

        $input['price'] = $input['price'] / $sign->value;
        $input['previous_price'] = $input['previous_price'] / $sign->value;

        // store filtering attributes for physical product
        $attrArr = [];
        if (!empty($request->category_id)) {
            $catAttrs = Attribute::where('attributable_id', $request->category_id)->where('attributable_type', 'App\Models\Category')->get();
            if (!empty($catAttrs)) {
                foreach ($catAttrs as $key => $catAttr) {
                    $in_name = $catAttr->input_name;
                    if ($request->has("$in_name")) {
                        $attrArr["$in_name"]["values"] = $request["$in_name"];
                        foreach ($request["$in_name" . "_price"] as $aprice) {
                            $ttt["$in_name" . "_price"][] = $aprice / $sign->value;
                        }
                        $attrArr["$in_name"]["prices"] = $ttt["$in_name" . "_price"];
                        if ($catAttr->details_status) {
                            $attrArr["$in_name"]["details_status"] = 1;
                        } else {
                            $attrArr["$in_name"]["details_status"] = 0;
                        }
                    }
                }
            }
        }

        if (!empty($request->subcategory_id)) {
            $subAttrs = Attribute::where('attributable_id', $request->subcategory_id)->where('attributable_type', 'App\Models\Subcategory')->get();
            if (!empty($subAttrs)) {
                foreach ($subAttrs as $key => $subAttr) {
                    $in_name = $subAttr->input_name;
                    if ($request->has("$in_name")) {
                        $attrArr["$in_name"]["values"] = $request["$in_name"];
                        foreach ($request["$in_name" . "_price"] as $aprice) {
                            $ttt["$in_name" . "_price"][] = $aprice / $sign->value;
                        }
                        $attrArr["$in_name"]["prices"] = $ttt["$in_name" . "_price"];
                        if ($subAttr->details_status) {
                            $attrArr["$in_name"]["details_status"] = 1;
                        } else {
                            $attrArr["$in_name"]["details_status"] = 0;
                        }
                    }
                }
            }
        }
        if (!empty($request->childcategory_id)) {
            $childAttrs = Attribute::where('attributable_id', $request->childcategory_id)->where('attributable_type', 'App\Models\Childcategory')->get();
            if (!empty($childAttrs)) {
                foreach ($childAttrs as $key => $childAttr) {
                    $in_name = $childAttr->input_name;
                    if ($request->has("$in_name")) {
                        $attrArr["$in_name"]["values"] = $request["$in_name"];
                        foreach ($request["$in_name" . "_price"] as $aprice) {
                            $ttt["$in_name" . "_price"][] = $aprice / $sign->value;
                        }
                        $attrArr["$in_name"]["prices"] = $ttt["$in_name" . "_price"];
                        if ($childAttr->details_status) {
                            $attrArr["$in_name"]["details_status"] = 1;
                        } else {
                            $attrArr["$in_name"]["details_status"] = 0;
                        }
                    }
                }
            }
        }

        if (empty($attrArr)) {
            $input['attributes'] = null;
        } else {
            $jsonAttr = json_encode($attrArr);
            $input['attributes'] = $jsonAttr;
        }
        if ($request->cross_products) {
            $input['cross_products'] = implode(',', $request->cross_products);
        }

        $input['brand_id'] = $request->brand_id;

        $data->slug = product_make_slug($data->name, (string) $data->sku);

        $data->update($input);
        //-- Logic Section Ends

        //--- Redirect Section
        $msg = __("Product Updated Successfully.") . '<a href="' . route('admin-prod-index') . '">' . __("View Product Lists.") . '</a>';
        return response()->json($msg);
        //--- Redirect Section Ends
    }

    //*** GET Request
    public function feature($id)
    {
        $data = Product::findOrFail($id);
        return view('admin.product.highlight', compact('data'));
    }

    //*** POST Request
    public function featuresubmit(Request $request, $id)
    {
        //-- Logic Section
        $data = Product::findOrFail($id);
        $input = $request->all();
        if ($request->featured == "") {
            $input['featured'] = 0;
        }
        if ($request->hot == "") {
            $input['hot'] = 0;
        }
        if ($request->best == "") {
            $input['best'] = 0;
        }
        if ($request->top == "") {
            $input['top'] = 0;
        }
        if ($request->latest == "") {
            $input['latest'] = 0;
        }
        if ($request->big == "") {
            $input['big'] = 0;
        }
        if ($request->trending == "") {
            $input['trending'] = 0;
        }
        if ($request->sale == "") {
            $input['sale'] = 0;
        }
        if ($request->is_discount == "") {
            $input['is_discount'] = 0;
            $input['discount_date'] = null;
        } else {
            $input['discount_date'] = \Carbon\Carbon::parse($input['discount_date'])->format('Y-m-d');
        }

        $data->update($input);
        //-- Logic Section Ends

        //--- Redirect Section
        $msg = __('Highlight Updated Successfully.');
        return response()->json($msg);
        //--- Redirect Section Ends

    }

    //*** DELETE Request
    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $this->deleteProduct($product);

        return response()->json(__('Product Deleted Successfully.'));
    }

    public function destroyAll()
    {
        @set_time_limit(0);

        $count = 0;

        Product::with(['galleries', 'reports', 'ratings', 'wishlists', 'clicks', 'comments.replies'])
            ->orderBy('id')
            ->chunkById(100, function ($products) use (&$count) {
                foreach ($products as $product) {
                    $this->deleteProduct($product);
                    $count++;
                }
            });

        return response()->json(__('All products deleted successfully.') . ' (' . $count . ')');
    }

    private function deleteProduct(Product $data): void
    {
        if ($data->galleries->count() > 0) {
            foreach ($data->galleries as $gal) {
                if (file_exists(public_path() . '/assets/images/galleries/' . $gal->photo)) {
                    unlink(public_path() . '/assets/images/galleries/' . $gal->photo);
                }
                $gal->delete();
            }
        }

        if ($data->reports->count() > 0) {
            foreach ($data->reports as $gal) {
                $gal->delete();
            }
        }

        if ($data->ratings->count() > 0) {
            foreach ($data->ratings as $gal) {
                $gal->delete();
            }
        }

        if ($data->wishlists->count() > 0) {
            foreach ($data->wishlists as $gal) {
                $gal->delete();
            }
        }

        if ($data->clicks->count() > 0) {
            foreach ($data->clicks as $gal) {
                $gal->delete();
            }
        }

        if ($data->comments->count() > 0) {
            foreach ($data->comments as $gal) {
                if ($gal->replies->count() > 0) {
                    foreach ($gal->replies as $key) {
                        $key->delete();
                    }
                }
                $gal->delete();
            }
        }

        if (Schema::hasTable('notifications')) {
            DB::table('notifications')->where('product_id', $data->id)->delete();
        }

        if (Schema::hasTable('quote_requests')) {
            DB::table('quote_requests')->where('product_id', $data->id)->delete();
        }

        if (! filter_var($data->photo, FILTER_VALIDATE_URL)) {
            if ($data->photo && $data->photo !== 'noimage.png') {
                if (file_exists(public_path() . '/assets/images/products/' . $data->photo)) {
                    unlink(public_path() . '/assets/images/products/' . $data->photo);
                }
            }
        }

        if ($data->thumbnail && $data->thumbnail !== 'noimage.png') {
            if (file_exists(public_path() . '/assets/images/thumbnails/' . $data->thumbnail)) {
                unlink(public_path() . '/assets/images/thumbnails/' . $data->thumbnail);
            }
        }

        if ($data->file != null) {
            if (file_exists(public_path() . '/assets/files/' . $data->file)) {
                unlink(public_path() . '/assets/files/' . $data->file);
            }
        }

        $data->delete();
    }

    public function catalog($id1, $id2)
    {
        $data = Product::findOrFail($id1);
        $data->is_catalog = $id2;
        $data->update();
        if ($id2 == 1) {
            $msg = "Product added to catalog successfully.";
        } else {
            $msg = "Product removed from catalog successfully.";
        }
        return response()->json($msg);
    }

    public function settingUpdate(Request $request)
    {
        //--- Logic Section
        $input = $request->all();
        $data = \App\Models\Generalsetting::findOrFail(1);

        if (!empty($request->product_page)) {
            $input['product_page'] = implode(',', $request->product_page);
        } else {
            $input['product_page'] = null;
        }

        if (!empty($request->wishlist_page)) {
            $input['wishlist_page'] = implode(',', $request->wishlist_page);
        } else {
            $input['wishlist_page'] = null;
        }

        cache()->forget('generalsettings');

        $data->update($input);
        //--- Logic Section Ends

        //--- Redirect Section
        $msg = __('Data Updated Successfully.');
        return response()->json($msg);
        //--- Redirect Section Ends
    }

    public function getAttributes(Request $request)
    {
        $model = '';
        if ($request->type == 'category') {
            $model = 'App\Models\Category';
        } elseif ($request->type == 'subcategory') {
            $model = 'App\Models\Subcategory';
        } elseif ($request->type == 'childcategory') {
            $model = 'App\Models\Childcategory';
        }

        $attributes = Attribute::where('attributable_id', $request->id)->where('attributable_type', $model)->get();
        $attrOptions = [];
        foreach ($attributes as $key => $attribute) {
            $options = AttributeOption::where('attribute_id', $attribute->id)->get();
            $attrOptions[] = ['attribute' => $attribute, 'options' => $options];
        }
        return response()->json($attrOptions);
    }

    public function getCrossProduct($catId)
    {
        $crossProducts = Product::where('category_id', $catId)->where('status', 1)->get();
        return view('load.cross_product', compact('crossProducts'));
    }

}
