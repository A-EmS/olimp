<?php
namespace app\repositories;

use app\models\ObjectMainValues;

class ObjectMainValuesRep extends ObjectMainValues
{
    public static function checkDuplicateByName($name, $countryId, $exceptedId = null)
    {
        $item = self::findOne(['name' => $name, 'country_id' => $countryId]);
        return ($item !== null && $item->id != $exceptedId && !empty($item->name));
    }

    public static function checkDuplicateByShortName($shortName, $countryId, $exceptedId = null)
    {
        $item = self::findOne(['short_name' => $shortName, 'country_id' => $countryId]);
        return ($item !== null && $item->id != $exceptedId && !empty($item->name));
    }
}