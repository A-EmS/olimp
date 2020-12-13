<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user".
 *
 * @property int $user_id
 * @property string $user_name
 * @property string $user_pwd
 * @property string $user_real
 * @property int $user_level
 * @property string $user_authKey
 * @property string $user_accessToken
 * @property string $notice
 * @property int $individual_id
 * @property string|null $user_create_user
 * @property string|null $user_create_time
 * @property string|null $user_create_ip
 * @property string|null $user_update_user
 * @property string|null $user_update_time
 * @property string|null $user_update_ip
 *
 * @property UserSettings[] $userSettings
 */
class User extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_name', 'user_pwd', 'user_real', 'notice', 'individual_id'], 'required'],
            [['user_level', 'individual_id'], 'integer'],
            [['user_create_time', 'user_update_time'], 'safe'],
            [['user_name', 'user_pwd', 'user_real', 'user_authKey', 'user_accessToken'], 'string', 'max' => 128],
            [['notice'], 'string', 'max' => 1000],
            [['user_create_user', 'user_create_ip', 'user_update_user', 'user_update_ip'], 'string', 'max' => 64],
            [['user_name', 'user_pwd'], 'unique', 'targetAttribute' => ['user_name', 'user_pwd']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'user_id' => 'User ID',
            'user_name' => 'User Name',
            'user_pwd' => 'User Pwd',
            'user_real' => 'User Real',
            'user_level' => 'User Level',
            'user_authKey' => 'User Auth Key',
            'user_accessToken' => 'User Access Token',
            'notice' => 'Notice',
            'individual_id' => 'Individual ID',
            'user_create_user' => 'User Create User',
            'user_create_time' => 'User Create Time',
            'user_create_ip' => 'User Create Ip',
            'user_update_user' => 'User Update User',
            'user_update_time' => 'User Update Time',
            'user_update_ip' => 'User Update Ip',
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
    public function getUserSettings()
    {
        return $this->hasMany(UserSettings::className(), ['user_id' => 'user_id']);
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
