<?php
namespace app\repositories;

use app\models\AcRole;
use app\models\User;
use Yii;

class UsersRep extends User
{

    public static function checkDuplicateByLogin(string $login, $exceptedId = null)
    {
        $item = self::findOne(['user_name' => $login]);
        return ($item !== null && $item->user_id != $exceptedId && !empty($item->user_name));
    }

    public static function checkDuplicateByIndividualId(string $individualId, $exceptedId = null)
    {
        $item = self::findOne(['individual_id' => $individualId]);
        return ($item !== null && $item->user_id != $exceptedId && !empty($item->user_name));
    }
}