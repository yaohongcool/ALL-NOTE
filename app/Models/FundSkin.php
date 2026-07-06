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

    public function rentals(): HasMany
    {
        return $this->hasMany(FundRental::class, 'skin_id');
    }

    public function earnings(): HasMany
    {
        return $this->hasMany(FundSkinEarning::class);
    }
}
