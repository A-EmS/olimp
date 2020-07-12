<?php
namespace app\repositories;

use app\models\AcRole;
use app\models\Products;
use app\models\Services;
use Yii;

class ServicesRep extends Services
{
    public static function checkDuplicateByName($name, $exceptedId = null)
    {
        $item = self::findOne(['name' => $name]);
        return ($item !== null && $item->id != $exceptedId && !empty($item->name));
    }
}