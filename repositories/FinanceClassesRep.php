<?php
namespace app\repositories;

use app\models\Banks;
use app\models\Contacts;
use app\models\FinanceClasses;
use Yii;

class FinanceClassesRep extends FinanceClasses
{
    public static function checkDuplicateByName($name, $exceptedId = null)
    {
        $item = self::findOne(['name' => $name]);
        return ($item !== null && $item->id != $exceptedId && !empty($item->name));
    }
}