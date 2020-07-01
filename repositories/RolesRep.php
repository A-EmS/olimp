<?php
namespace app\repositories;

use app\models\AcRole;
use Yii;

class RolesRep extends AcRole
{
    public static function checkDuplicateByName($name, $exceptedId = null)
    {
        $item = self::findOne(['name' => $name]);
        return ($item !== null && $item->id != $exceptedId && !empty($item->name));
    }
}