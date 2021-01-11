<?php
namespace app\repositories;

use app\models\AcRole;
use app\models\CurrencyExchangeRates;
use Yii;

class CurrencyExchangeRatesRep extends CurrencyExchangeRates
{
    public static function checkDuplicateByCurrenciesAndDate($currency_id_base, $currency_id_ref, $date_from, $exceptedId = null)
    {
        $item = self::findOne(['currency_id_base' => $currency_id_base, 'currency_id_ref' => $currency_id_ref, 'date_from' => $date_from]);
        return ($item !== null && $item->id != $exceptedId && !empty($item->id));
    }

    public static function checkDuplicateByCurrenciesAndEmptyEndDate($currency_id_base, $currency_id_ref, $exceptedId = null)
    {
        $item = self::findOne(['currency_id_base' => $currency_id_base, 'currency_id_ref' => $currency_id_ref, 'date_to' => null]);
        return ($item !== null && $item->id != $exceptedId && !empty($item->id));
    }
}