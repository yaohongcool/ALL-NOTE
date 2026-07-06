<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FundMonthly extends Model
{
    protected $fillable = [
        'user_id',
        'month',
        'income',
        'expense',
        'savings_target',
        'savings_actual',
        'savings_status',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'month' => 'date',
            'income' => 'decimal:2',
            'expense' => 'decimal:2',
            'savings_target' => 'decimal:2',
            'savings_actual' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
