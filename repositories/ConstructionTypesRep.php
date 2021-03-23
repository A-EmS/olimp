<?php
namespace app\repositories;

use app\models\ConstructionTypes;

class ConstructionTypesRep extends ConstructionTypes
{
    public static function checkDuplicate($name, $countryId, $exceptedId = null)
    {
        $item = self::findOne(['name' => $name, 'country_id' => $countryId]);
        return ($item !== null && $item->id != $exceptedId && !empty($item->name));
    }
}