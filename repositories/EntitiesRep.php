<?php
namespace app\repositories;

use app\models\Entities;

class EntitiesRep extends Entities
{
    public static function existByINN($INN, $exceptedId = null)
    {
        $item = self::findOne(['inn' => $INN]);
        return ($item !== null && $item->id != $exceptedId);
    }

    public static function existByOGRN($ogrn, $exceptedId = null)
    {
        $item = self::findOne(['ogrn' => $ogrn]);
        return ($item !== null && $item->id != $exceptedId);
    }
}