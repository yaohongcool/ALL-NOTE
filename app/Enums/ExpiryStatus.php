<?php

namespace App\Enums;

enum ExpiryStatus: string
{
    case Normal = '正常';
    case Expiring = '即将到期';
    case Expired = '已过期';

    public function label(): string
    {
        return $this->value;
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Normal => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300',
            self::Expiring => 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300',
            self::Expired => 'bg-red-50 text-red-700 dark:bg-red-950/40 dark:text-red-300',
        };
    }
}
