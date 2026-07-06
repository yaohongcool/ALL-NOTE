<?php

namespace App\Enums;

enum EventVisibility: string
{
    case Private = 'private';
    case Public = 'public';

    public function label(): string
    {
        return match ($this) {
            self::Private => '仅自己可见',
            self::Public => '公开',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Public => 'bg-indigo-50 text-indigo-700 dark:bg-indigo-950/40 dark:text-indigo-300',
            self::Private => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
