<?php

namespace App\Helpers;

use App\Models\BusinessSetting;

class BusinessHelper
{

    static function getTaxFromOrder($amount): float
    {
        return self::_calculate($amount, BusinessSetting::getTax(), BusinessSetting::getTaxType());
    }

    static function _calculate($amount, $value, $type): float
    {
        if ($type == 'percent') {
            return round(($amount * $value) / 100, 2);
        } else {
            return round($value, 2);
        }
    }


    static function getDeliveryCommissionFromOrder($amount): float
    {
        return self::_calculate($amount, BusinessSetting::_get(BusinessSetting::$delivery_commission), BusinessSetting::_get(BusinessSetting::$delivery_commission_type));
    }

    static function getPaymentChargeFromOrder($amount): float
    {
        return self::_calculate($amount, BusinessSetting::_get(BusinessSetting::$payment_charge), BusinessSetting::_get(BusinessSetting::$payment_charge_type));
    }

    public static function setData(array $validated_data): void
    {
        foreach ($validated_data as $key => $value) {
            BusinessSetting::_set($key, $value);
        }
        BusinessSetting::updateCache();
    }


}
