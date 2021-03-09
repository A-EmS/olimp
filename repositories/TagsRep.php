<?php
namespace app\repositories;

use app\models\AcRole;
use app\models\Tags;
use Yii;

class TagsRep extends Tags
{
    public static function checkDuplicateByName($name, $exceptedId = null)
    {
        $item = self::findOne(['name' => $name]);
        return ($item !== null && $item->id != $exceptedId && !empty($item->name));
    }
}