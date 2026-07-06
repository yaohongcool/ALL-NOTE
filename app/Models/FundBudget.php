<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FundBudget extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'type',
        'monthly_amount',
        'annual_amount',
        'note',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
