<?php

namespace App\Enums;

enum FundBudgetType: string
{
    case Income = 'income';
    case Expense = 'expense';

    public function label(): string
    {
        return match ($this) {
            self::Income => '收入',
            self::Expense => '支出',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
