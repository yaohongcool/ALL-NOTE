<?php

namespace App\Enums;

enum FundSavingStatus: string
{
    case Completed = 'completed';
    case Uncompleted = 'uncompleted';
    case NA = 'na';

    public function label(): string
    {
        return match ($this) {
            self::Completed => '达成',
            self::Uncompleted => '未达成',
            self::NA => '不适用',
        };
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Completed => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300',
            self::Uncompleted => 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300',
            self::NA => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
