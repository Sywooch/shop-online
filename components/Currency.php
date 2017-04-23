<?php

namespace app\components;

use yii\base\Component;

class Currency extends Component
{
    const KZT = "KZT";
    const USD = "USD";
    const RUR = "RUR";
    const RUB = "RUB";

    const RUR_KZT = 5.0;
    const USD_KZT = 320.0;

    public static function kzt($price, $currency = self::RUR)
    {
        switch ($currency) {
            case self::USD:
                $price_kzt = $price * self::USD_KZT;
                break;
            case self::RUB:
            case self::RUR:
                $price_kzt = $price * self::RUR_KZT;
                break;
            default:
                $price_kzt = $price;
        }

        $price_kzt = number_format($price_kzt, 0, ',', ' ');

        return $price_kzt;
    }
}