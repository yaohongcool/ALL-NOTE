<?php

namespace App\Enums;

enum EventStatus: string
{
    case Processed = '已处理';
    case Processing = '处理中';
    case Pending = '待处理';
    case NoAction = '无需处理';

    public function label(): string
    {
        return $this->value;
    }

    public function badgeClass(): string
    {
        return match ($this) {
            self::Processed => 'bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300',
            self::Processing => 'bg-blue-50 text-blue-700 dark:bg-blue-950/40 dark:text-blue-300',
            self::Pending => 'bg-amber-50 text-amber-700 dark:bg-amber-950/40 dark:text-amber-300',
            self::NoAction => 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-300',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
