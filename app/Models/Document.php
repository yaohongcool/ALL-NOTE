<?php

namespace App\Models;

use App\Enums\ExpiryStatus;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{

    protected $fillable = [
        'user_id',
        'name',
        'category',
        'status',
        'due_date',
        'note',
    ];

    protected $appends = [
        'computed_status',
        'days_until_due',
        'days_until_due_label',
    ];

    protected function casts(): array
    {
        return [
            'due_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
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
