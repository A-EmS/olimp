<?php
namespace app\repositories;

use app\models\AcRole;
use app\models\User;
use Yii;

class UsersRep extends User
{
    public static function checkDuplicateByLoginAndPassword($login, $password, $exceptedId = null)
    {
        $item = self::findOne(['user_name' => $login, 'user_pwd' => $password,]);
        return ($item !== null && $item->user_id != $exceptedId && !empty($item->user_name));
    }
}