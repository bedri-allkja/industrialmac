<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductImport extends Model
{
    protected $fillable = [
        'admin_id',
        'original_name',
        'file_path',
        'status',
        'total_rows',
        'processed_rows',
        'last_row',
        'imported_count',
        'skipped_count',
        'error_count',
        'retry_count',
        'skip_images',
        'background',
        'message',
        'log',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'skip_images' => 'boolean',
        'background' => 'boolean',
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function progressPercent(): float
    {
        if ($this->total_rows <= 0) {
            return $this->status === 'completed' ? 100 : 0;
        }

        return min(100, round(($this->processed_rows / $this->total_rows) * 100, 2));
    }

    public function elapsedSeconds(): ?int
    {
        if (! $this->started_at) {
            return null;
        }

        $end = $this->finished_at ?? now();

        return $this->started_at->diffInSeconds($end);
    }

    public function rowsPerSecond(): float
    {
        $elapsed = $this->elapsedSeconds();

        if (! $elapsed || $this->processed_rows <= 0) {
            return 0;
        }

        return round($this->processed_rows / $elapsed, 2);
    }

    public function toStatusArray(): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status,
            'original_name' => $this->original_name,
            'total_rows' => $this->total_rows,
            'processed_rows' => $this->processed_rows,
            'last_row' => $this->last_row,
            'imported_count' => $this->imported_count,
            'skipped_count' => $this->skipped_count,
            'error_count' => $this->error_count,
            'retry_count' => $this->retry_count,
            'progress_percent' => $this->progressPercent(),
            'elapsed_seconds' => $this->elapsedSeconds(),
            'rows_per_second' => $this->rowsPerSecond(),
            'message' => $this->message,
            'log' => $this->log,
            'started_at' => optional($this->started_at)->toDateTimeString(),
            'finished_at' => optional($this->finished_at)->toDateTimeString(),
            'is_running' => in_array($this->status, ['pending', 'running'], true),
        ];
    }
}
