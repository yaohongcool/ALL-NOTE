<?php

namespace App\Enums;

enum DocumentCategory: string
{
    case Certificate = '证件';
    case Membership = '会员';
    case Item = '物品';
    case Other = '其它';

    public function label(): string
    {
        return $this->value;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
