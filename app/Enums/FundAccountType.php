<?php

namespace App\Enums;

enum FundAccountType: string
{
    case Cash = 'cash';
    case Platform = 'platform';
    case Wechat = 'wechat';
    case Virtual = 'virtual';
    case Credit = 'credit';
    case Receivable = 'receivable';
    case HousingBase = 'housing_base';
    case HousingPayment = 'housing_payment';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Cash => '现金账户',
            self::Platform => '平台账户',
            self::Wechat => '微信支付',
            self::Virtual => '虚拟资产',
            self::Credit => '信用卡',
            self::Receivable => '应收款',
            self::HousingBase => '公积金基数',
            self::HousingPayment => '公积金缴额',
            self::Other => '其他',
        };
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
