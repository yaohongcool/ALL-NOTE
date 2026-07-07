<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FundSkin extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'purchased_at',
        'cost',
        'uu_price',
        'uu_fee_rate',
        'buff_price',
        'buff_fee_rate',
        'daily_rental',
        'note',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected function casts(): array
    {
        return [
            'cost' => 'decimal:2',
            'uu_price' => 'decimal:2',
            'uu_fee_rate' => 'decimal:4',
            'buff_price' => 'decimal:2',
            'buff_fee_rate' => 'decimal:4',
            'daily_rental' => 'decimal:2',
            'purchased_at' => 'date',
        ];
    }

    public function rentals(): HasMany
    {
        return $this->hasMany(FundRental::class, 'skin_id');
    }

    public function earnings(): HasMany
    {
        return $this->hasMany(FundSkinEarning::class);
    }
}
