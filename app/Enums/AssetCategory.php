<?php

namespace App\Enums;

enum AssetCategory: string
{
    case Physical = '物理设备';
    case Server = '云服务器';
    case Domain = '域名';

    public function label(): string
    {
        return $this->value;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
