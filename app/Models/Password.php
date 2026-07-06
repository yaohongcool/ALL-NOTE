<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Password extends Model
{

    protected $fillable = [
        'user_id',
        'name',
        'account',
        'encrypted_password',
        'phone',
        'email',
        'note',
    ];

    protected $hidden = [
        'encrypted_password',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
