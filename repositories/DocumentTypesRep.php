<?php
namespace app\repositories;

use app\models\DocumentTypes;
use Yii;

class DocumentTypesRep extends DocumentTypes
{
    public static function checkDuplicateByCountryAndName(int $countryId, string $name, $exceptedId = null)
    {
        $item = self::findOne(['country_id' => $countryId, 'name' => $name]);
        return ($item !== null && $item->id != $exceptedId && !empty($item->name));
    }
}