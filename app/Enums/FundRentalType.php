<?php

namespace App\Enums;

enum FundRentalType: string
{
    case Short = '短租';
    case Long = '长租';
    case EventShort = '活动短租';
    case EventLong = '活动长租';

    public function label(): string
    {
        return $this->value;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
