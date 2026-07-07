<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }

    public function passwords(): HasMany
    {
        return $this->hasMany(Password::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function eventTags(): HasMany
    {
        return $this->hasMany(EventTag::class);
    }

    public function fundAccounts(): HasMany
    {
        return $this->hasMany(FundAccount::class);
    }

    public function fundBudgets(): HasMany
    {
        return $this->hasMany(FundBudget::class);
    }

    public function fundMonthlies(): HasMany
    {
        return $this->hasMany(FundMonthly::class);
    }

    public function fundSkins(): HasMany
    {
        return $this->hasMany(FundSkin::class);
    }

    public function fundRentals(): HasMany
    {
        return $this->hasMany(FundRental::class);
    }

    public function fundSkinEarnings(): HasMany
    {
        return $this->hasMany(FundSkinEarning::class);
    }

    public function fundEarningPeriods(): HasMany
    {
        return $this->hasMany(FundEarningPeriod::class);
    }
}
