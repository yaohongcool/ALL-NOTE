<?php

namespace App\Models;

use App\Models\FundEarningPeriod;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FundSkinEarning extends Model
{
    protected $fillable = [
        'period_id',
        'skin_id',
        'user_id',
        'month',
        'revenue',
        'original_amount',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'month' => 'date',
            'revenue' => 'decimal:2',
            'original_amount' => 'decimal:2',
        ];
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(FundEarningPeriod::class, 'period_id');
    }

    public function skin(): BelongsTo
    {
        return $this->belongsTo(FundSkin::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
