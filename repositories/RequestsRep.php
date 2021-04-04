<?php
namespace app\repositories;

use app\models\Requests;

class RequestsRep extends Requests
{
    public static function checkDuplicateByNameAndCountry($name, $countryId, $exceptedId = null)
    {
        $item = self::findOne(['name' => $name, 'country_id' => $countryId]);
        return ($item !== null && $item->id != $exceptedId && !empty($item->name));
    }
}