<?php
namespace app\repositories;

use app\models\Prices;

class PricesRep extends Prices
{
    public static function checkDuplicateByPriceList($priceListId, $exceptedId = null)
    {
        $item = self::findOne(['price_list_id' => $priceListId]);
        return ($item !== null && $item->id != $exceptedId && !empty($item->id));
    }
}