<?php
namespace app\repositories;

use app\models\PriceLists;

class PriceListsRep extends PriceLists
{
    public static function checkDuplicateByName($name, $exceptedId = null)
    {
        $item = self::findOne(['name' => $name]);
        return ($item !== null && $item->id != $exceptedId && !empty($item->name));
    }
}