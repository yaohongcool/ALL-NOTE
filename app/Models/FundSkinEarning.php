<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FundSkinEarning extends Model
{
    protected $fillable = [
        'skin_id',
        'user_id',
        'month',
        'revenue',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'month' => 'date',
            'revenue' => 'decimal:2',
        ];
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
