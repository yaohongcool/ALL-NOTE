<?php

namespace App\Models;

use App\Enums\AssetCategory;
use App\Enums\ExpiryStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asset extends Model
{

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
            AssetCategory::Physical->value => trim(collect([
                $this->getDetail('cpu_model'),
                $this->getDetail('gpu_model'),
                $this->getDetail('memory') ? $this->getDetail('memory') . 'GB' : null,
                $this->getDetail('storage_1') ?  $this->getDetail('storage_1') . 'TB' : null,
                $this->getDetail('storage_2') ?  $this->getDetail('storage_2') . 'TB' : null,
                $this->getDetail('storage_3') ?  $this->getDetail('storage_3') . 'TB' : null,
            ])->filter()->implode(' / ')),
            AssetCategory::Server->value => trim(collect([
                $this->getDetail('ip_address'),
                $this->getDetail('operating_system'),
                $this->getDetail('provider'),
            ])->filter()->implode(' / ')),
            AssetCategory::Domain->value => (string) $this->getDetail('domain_address', ''),
            default => '',
        };
    }

    public function getComputedStatusAttribute(): string
    {
        $days = $this->days_until_due;

        if ($days === null) {
            return ExpiryStatus::Normal->value;
        }

        if ($days < 0) {
            return ExpiryStatus::Expired->value;
        }

        if ($days <= 60) {
            return ExpiryStatus::Expiring->value;
        }

        return ExpiryStatus::Normal->value;
    }

    public function getDaysUntilDueAttribute(): ?int
    {
        if (! $this->due_date) {
            return null;
        }

        $tz = config('app.display_timezone', 'Asia/Shanghai');
        $today = Carbon::today($tz);
        $dueDate = Carbon::parse($this->due_date->format('Y-m-d'), $tz)->startOfDay();

        return (int) $today->diffInDays($dueDate, false);
    }

    public function getDaysUntilDueLabelAttribute(): string
    {
        return $this->days_until_due === null ? '-' : $this->days_until_due . '天';
    }
}
