<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Document extends Model
{
    use HasFactory;

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
