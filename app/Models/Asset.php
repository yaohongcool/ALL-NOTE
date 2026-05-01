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
        'days_until_due',
        'days_until_due_label',
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
        $days = $this->days_until_due;

        if ($days === null) {
            return '正常';
        }

        if ($days < 0) {
            return '已过期';
        }

        if ($days <= 60) {
            return '即将到期';
        }

        return '正常';
    }

    public function getDaysUntilDueAttribute(): ?int
    {
        if (! $this->due_date) {
            return null;
        }

        $today = Carbon::today('Asia/Shanghai');
        $dueDate = Carbon::parse($this->due_date->format('Y-m-d'), 'Asia/Shanghai')->startOfDay();

        return (int) $today->diffInDays($dueDate, false);
    }

    public function getDaysUntilDueLabelAttribute(): string
    {
        return $this->days_until_due === null ? '-' : $this->days_until_due . '天';
    }
}
