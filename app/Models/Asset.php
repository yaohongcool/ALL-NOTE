<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category',
        'name',
        'status',
        'due_date',
        'details_json',
        'note',
    ];

    protected $appends = [
        'summary',
        'computed_status',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
            'details_json' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getDetail(string $key, mixed $default = ''): mixed
    {
        return data_get($this->details_json ?? [], $key, $default);
    }

    public function getSummaryAttribute(): string
    {
        return match ($this->category) {
            '物理设备' => trim(collect([
                $this->getDetail('cpu_model'),
                $this->getDetail('gpu_model'),
                $this->getDetail('memory') ? $this->getDetail('memory') . 'GB' : null,
                $this->getDetail('storage_1') ?  $this->getDetail('storage_1') . 'TB' : null,
                $this->getDetail('storage_2') ?  $this->getDetail('storage_2') . 'TB' : null,
                $this->getDetail('storage_3') ?  $this->getDetail('storage_3') . 'TB' : null,
            ])->filter()->implode(' / ')),
            '云服务器' => trim(collect([
                $this->getDetail('ip_address'),
                $this->getDetail('operating_system'),
                $this->getDetail('provider'),
            ])->filter()->implode(' / ')),
            '域名' => (string) $this->getDetail('domain_address', ''),
            default => '',
        };
    }

    public function getComputedStatusAttribute(): string
    {
        if (! $this->due_date) {
            return '正常';
        }

        $today = Carbon::today();
        $dueDate = $this->due_date instanceof Carbon ? $this->due_date->copy()->startOfDay() : Carbon::parse($this->due_date)->startOfDay();
        $days = $today->diffInDays($dueDate, false);

        if ($days < 0) {
            return '已过期';
        }

        if ($days <= 60) {
            return '即将到期';
        }

        return '正常';
    }
}