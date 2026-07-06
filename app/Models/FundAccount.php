<?php

namespace App\Models;

use App\Enums\FundAccountType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FundAccount extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'type',
        'balance',
        'sort',
        'note',
    ];

    protected function casts(): array
    {
        return [
            'type' => FundAccountType::class,
            'balance' => 'decimal:2',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
