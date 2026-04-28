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
        if (! $this->due_date) {
            return '正常';
        }

        $today = Carbon::today();
        $dueDate = $this->due_date instanceof Carbon
            ? $this->due_date->copy()->startOfDay()
            : Carbon::parse($this->due_date)->startOfDay();

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