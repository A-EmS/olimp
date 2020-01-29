<?php
/**
 * @author Ruslan Bondarenko (Dnipro) r.i.bondarenko@gmail.com
 * @copyright Copyright (C) 2016-2017 Ruslan Bondarenko (Dnipro)
 * @license http://www.yiiframework.com/license/
 */

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

class UserI extends \yii\base\BaseObject implements \yii\web\IdentityInterface
{

    public $id;
    public $username;
    public $password;
    public $level;
    public $authKey;
    public $accessToken;
    public $role;
    public $roles;
    public $settings;
    public $accessActions;
    public $isAdmin;


    /**
     * @inheritdoc
     */
    public static function findIdentity($id)
    {
        //return isset(self::$user[$id]) ? new static(self::$user[$id]) : null;

        $model = (new \yii\db\Query())
            ->select('*')
            ->from('user')
            ->leftJoin('ac_user_role', 'user.user_id = ac_user_role.acur_user_id')
            ->where('user_id=:user_id', [':user_id' => $id])
            ->all();

        $roles = (new \yii\db\Query())
            ->select('acur_acr_id')
            ->from('ac_user_role')
            ->where('acur_user_id=:user_id', [':user_id' => $id])
            ->all();

        $actionsRows = (new \yii\db\Query())
            ->select('ac_func.acf_controller, ac_func.acf_action')
            ->from('ac_role_func')
            ->leftJoin('ac_func', 'ac_func.acf_id = ac_role_func.acrf_acf_id')
            ->where(['acrf_acr_id' => ArrayHelper::map($roles, 'acur_acr_id', 'acur_acr_id')])
            ->all();

        $actions = [];
        foreach ($actionsRows as $actionsRow){
            $actions[$actionsRow['acf_controller'].'/'.$actionsRow['acf_action']] = [
                'acf_controller' => $actionsRow['acf_controller'],
                'acf_action' => $actionsRow['acf_action'],
                'acf_string' => $actionsRow['acf_controller'].'/'.$actionsRow['acf_action'],
            ];
        }

        $settings = (new \yii\db\Query())
            ->select('key, value')
            ->from('user_settings')
            ->where('user_id=:user_id', [':user_id' => $id])
            ->all();

        $settingsArray = [];
        foreach ($settings as $setting){
            if (self::isJSON($setting['value'])){
                $settingsArray[$setting['key']] = json_decode($setting['value']);
            } else {
                $settingsArray[$setting['key']] = $setting['value'];
            }

        }

        foreach ($model as $user) {
            if ($user['user_id'] == $id) {
                $us = array(
                         'id'           =>$user["user_id"],
                         'username'     =>$user["user_name"],
                         'password'     =>$user["user_pwd"],
                         'level'        =>$user["user_level"],
                         'accessActions'=>$actions,
                         'role'         =>$user["acur_acr_id"],
                         'roles'         =>ArrayHelper::map($roles, 'acur_acr_id', 'acur_acr_id'),
                         'settings'     => $settingsArray,
                         'isAdmin'         => (bool) $user["acur_acr_id"] == 0,
                         'authKey'      =>$user["user_authKey"],
                         'accessToken'  =>$user["user_accessToken"],
                );
                return new static($us);
            }
        }
        return null;

    }

     /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {

        $model = (new \yii\db\Query())
            ->select('*')
            ->from('user')
            ->leftJoin('ac_user_role', 'user.user_id = ac_user_role.acur_user_id')
            ->where([
                    'user_accessToken' => $token
                    ])
            ->all();

        foreach ($model as $user) {
            if ($user['accessToken'] === $token) {
                $us = array(
                         'id'           =>$user["user_id"], 
                         'username'     =>$user["user_name"], 
                         'password'     =>$user["user_pwd"], 
                         'level'        =>$user["user_level"],
                         'role'         =>$user["acur_acr_id"],
                         'isAdmin'         => (bool) $user["acur_acr_id"] == 0,
                         'authKey'      =>$user["user_authKey"],
                         'accessToken'  =>$user["user_accessToken"],
                );
                return new static($us);
            }
        }

        return null;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        $model = (new \yii\db\Query())
                ->select('*')
                ->from('user')
                ->leftJoin('ac_user_role', 'user.user_id = ac_user_role.acur_user_id')
                ->where('UPPER(user_name)=UPPER(:user_name)', [':user_name' => $username])
                ->all();

        foreach ($model as $user) {

            $roles = (new \yii\db\Query())
                ->select('acur_acr_id')
                ->from('ac_user_role')
                ->where('acur_user_id=:user_id', [':user_id' => $user["user_id"]])
                ->all();

            $actionsRows = (new \yii\db\Query())
                ->select('ac_func.acf_controller, ac_func.acf_action')
                ->from('ac_role_func')
                ->leftJoin('ac_func', 'ac_func.acf_id = ac_role_func.acrf_acf_id')
                ->where(['acrf_acr_id' => ArrayHelper::map($roles, 'acur_acr_id', 'acur_acr_id')])
                ->all();

            $settings = (new \yii\db\Query())
                ->select('key, value')
                ->from('user_settings')
                ->where('user_id=:user_id', [':user_id' => $user["user_id"]])
                ->all();

            $settingsArray = [];
            foreach ($settings as $setting){
                if (self::isJSON($setting['value'])){
                    $settingsArray[$setting['key']] = json_decode($setting['value']);
                } else {
                    $settingsArray[$setting['key']] = $setting['value'];
                }

            }

            $us = array(
                'id'           =>$user["user_id"],
                'username'     =>$user["user_name"],
                'password'     =>$user["user_pwd"],
                'level'        =>$user["user_level"],
                'role'         =>$user["acur_acr_id"],
                'roles'        =>ArrayHelper::map($roles, 'acur_acr_id', 'acur_acr_id'),
                'isAdmin'      => (bool) $user["acur_acr_id"] == 0,
                'settings'     => $settingsArray,
                'authKey'      =>$user["user_authKey"],
                'accessToken'  =>$user["user_accessToken"],
            );
            return new static($us);
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @inheritdoc
     */
    public function getAuthKey()
    {
        return $this->authKey;
    }

    /**
     * @inheritdoc
     */
    public function getRole()
    {
        return $this->role;
    }


    /**
     * @inheritdoc
     */
    public function validateAuthKey($authKey)
    {
        return $this->authKey === $authKey;
    }

    /**
     * Validates password
     *
     * @param  string  $password password to validate
     * @return boolean if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        if ($this->level < 0) {
            if (!empty($password) && !empty($this->password)) {
                return $password == $this->password;
            }
            return false;
        } else {
            return password_verify($password, $this->password);
            /*
            echo password_hash("GfhjkmFlvbyf", PASSWORD_BCRYPT, [12]);
            */
        }
    }

    public static function isJSON($string){
        return is_string($string) && is_array(json_decode($string, true)) && (json_last_error() == JSON_ERROR_NONE) ? true : false;
    }
}


