<?php
namespace app\repositories;

use app\models\Individuals;

class IndividualsRep extends Individuals
{
    public static function existByINN($INN)
    {
        $item = self::findOne(['inn' => $INN]);
        return ($item !== null);
    }

    public static function existByPassport($number, $series)
    {
        $item = self::findOne(['passport_number' => $number, 'passport_series' => $series]);
        return ($item !== null);
    }
}