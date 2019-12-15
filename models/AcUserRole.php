<?php
/**
 * @author Ruslan Bondarenko (Dnipro) r.i.bondarenko@gmail.com
 * @copyright Copyright (C) 2016-2017 Ruslan Bondarenko (Dnipro)
 * @license http://www.yiiframework.com/license/
 */


namespace app\models;

use Yii;

/**
 * This is the model class for table "ac_user_role".
 *
 * @property integer $acur_id
 * @property integer $acur_user_id
 * @property integer $acur_acr_id
 * @property string $acur_create_user
 * @property string $acur_create_time
 * @property string $acur_update_user
 * @property string $acur_update_time
 *
 * @property AcRole $role
 * @property User $user
 */
class AcUserRole extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%ac_user_role}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['acur_user_id', 'acur_acr_id'], 'required'],
            [['acur_user_id', 'acur_acr_id'], 'integer'],
            [['acur_create_time', 'acur_update_time'], 'safe'],
            [['acur_create_user', 'acur_update_user'], 'string', 'max' => 32]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'acur_id' => Yii::t('app', 'Acur ID'),
            'acur_user_id' => Yii::t('app', 'Acur User ID'),
            'acur_acr_id' => Yii::t('app', 'Acur Acr ID'),
            'acur_create_user' => Yii::t('app', 'Acur Create User'),
            'acur_create_time' => Yii::t('app', 'Acur Create Time'),
            'acur_update_user' => Yii::t('app', 'Acur Update User'),
            'acur_update_time' => Yii::t('app', 'Acur Update Time'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(AcRole::class, ['acr_id' => 'acur_acr_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::class, ['user_id' => 'acur_user_id']);
    }

    /**
     * @inheritdoc
     * @return AcUserRoleQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new AcUserRoleQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) return false;
        if ($insert) {
            $this->acur_create_time = date('Y-m-d H:i:s');
            $this->acur_create_user = Yii::$app->user->identity->username;
            return true;
        } else {
            $this->acur_update_time = date('Y-m-d H:i:s');
            $this->acur_update_user = Yii::$app->user->identity->username;
            return true;
        }
    }


    /**
     * Check for exists rule.
     * @param integer $user_id
     * @param integer $acr_id
     * @return boolean
     */
    public static function exists($user_id, $acr_id)
    {
        return AcUserRole::find()->where(['acur_user_id' => $user_id, 'acur_acr_id' => $acr_id, ])->exists();
    }

    /**
     * Check for exists rule.
     * @param integer $acr_id
     * @param integer $acf_id
     * @return AcRoleFunc()
     */
    public static function existsOne($user_id, $acr_id)
    {
        if (($model = AcUserRole::find()->where(['acur_user_id' => $user_id, 'acur_acr_id' => $acr_id, ])->one()) === null) {
            return false;
        }
        return $model;
    }

    public function updateRole($userId, $roleId)
    {
        AcUserRole::deleteAll("acur_user_id = $userId");

        $model = new AcUserRole();
        if($roleId != ''){
            $date = new \DateTime();
            $model->acur_user_id = $userId;
            $model->acur_acr_id = $roleId;
            $model->acur_create_user = Yii::$app->user->identity->username;
            $model->acur_create_time = $date->format('Y-m-d H:i:s');
            $model->save(false);
        }

        return json_encode(['lastId' => $model->acur_id]);
    }

}
