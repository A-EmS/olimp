<?php
namespace app\repositories;

use app\models\Individuals;

class IndividualsRep extends Individuals
{
    public static function existByINN($INN, $exceptedId = null)
    {
        $item = self::findOne(['inn' => $INN]);
        return ($item !== null && $item->id != $exceptedId);
    }

    public static function existByPassport($number, $series, $exceptedId = null)
    {
        $item = self::findOne(['passport_number' => $number, 'passport_series' => $series]);
        return ($item !== null && $item->id != $exceptedId);
    }
}