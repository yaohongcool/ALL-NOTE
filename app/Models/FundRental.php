<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FundRental extends Model
{
    protected $fillable = [
        'skin_id',
        'user_id',
        'type',
        'rate',
        'discount',
        'lease_days',
        'offhand_days',
        'fee_rate',
        'revenue',
        'note',
    ];

    public function skin(): BelongsTo
    {
        return $this->belongsTo(FundSkin::class, 'skin_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
