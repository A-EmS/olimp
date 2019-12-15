<?php
/**
 * @author Ruslan Bondarenko (Dnipro) r.i.bondarenko@gmail.com
 * @copyright Copyright (C) 2016-2017 Ruslan Bondarenko (Dnipro)
 * @license http://www.yiiframework.com/license/
 */

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property integer $user_id
 * @property string $user_name
 * @property string $user_pwd
 * @property string $user_real
 * @property integer $user_level
 * @property string $user_authKey
 * @property string $user_accessToken
 *
 * @property AcUserRole[] $UserRoles
 * @property Event[] $events
 * @property Task[] $tasks
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_name', 'user_real'], 'required'],
            [['user_level'], 'integer'],
            [['user_name', 'user_pwd', 'user_real', 'user_authKey', 'user_accessToken'], 'string', 'max' => 128],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'user_id' => Yii::t('app', 'User ID'),
            'user_name' => Yii::t('app', 'User Name'),
            'user_pwd' => Yii::t('app', 'User Pwd'),
            'user_real' => Yii::t('app', 'User Real'),
            'user_level' => Yii::t('app', 'User Level'),
            'user_authKey' => Yii::t('app', 'User Auth Key'),
            'user_accessToken' => Yii::t('app', 'User Access Token'),
            'user_create_time' => Yii::t('app', 'User Create Time'),
            'user_create_ip' => Yii::t('app', 'User Create Ip'),
            'user_update_user' => Yii::t('app', 'User Update User'),
            'user_update_time' => Yii::t('app', 'User Update Time'),
            'user_update_ip' => Yii::t('app', 'User Update Ip'),
            'user_create_user' => Yii::t('app', 'User Create User'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserRoles()
    {
        return $this->hasMany(AcUserRole::class, ['acur_user_id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvents()
    {
        return $this->hasMany(Event::class, ['event_user_id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(Task::class, ['task_user_id' => 'user_id']);
    }

    /**
     * @inheritdoc
     * @return UserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) return false;
        if ($insert) {
            $this->user_pwd = password_hash($this->user_pwd, PASSWORD_BCRYPT, [12]);
            $this->user_create_user = Yii::$app->user->identity->username;
            $this->user_create_ip = Yii::$app->request->userIP;
            $this->user_create_time = date('Y-m-d H:i:s');
            return true;
        } else {
            if ($this->user_pwd !== "") {
                $this->user_pwd = password_hash($this->user_pwd, PASSWORD_BCRYPT, [12]);
            } else {
                $this->user_pwd = $this->OldAttributes['user_pwd'];
            }
            $this->user_update_user = Yii::$app->user->identity->username;
            $this->user_update_ip = Yii::$app->request->userIP;
            $this->user_update_time = date('Y-m-d H:i:s');
            return true;
        }
    }

}
