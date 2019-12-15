<?php
/**
 * @author Ruslan Bondarenko (Dnipro) r.i.bondarenko@gmail.com
 * @copyright Copyright (C) 2016-2017 Ruslan Bondarenko (Dnipro)
 * @license http://www.yiiframework.com/license/
 */

namespace app\models;

use Yii;

/**
 * This is the model class for table "ac_role".
 *
 * @property integer $acr_id
 * @property string $acr_name
 * @property string $acr_desc
 * @property string $acr_create_user
 * @property string $acr_create_time
 * @property string $acr_create_ip
 * @property string $acr_update_user
 * @property string $acr_update_time
 * @property string $acr_update_ip
 *
 * @property AcRoleFunc[] $RoleFuncs
 * @property AcUserRole[] $UserRoles
 */
class AcRole extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%ac_role}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['acr_name'], 'required'],
            [['acr_create_time', 'acr_update_time'], 'safe'],
            [['acr_name'], 'string', 'max' => 64],
            [['acr_desc'], 'string', 'max' => 256],
            [['acr_create_user', 'acr_update_user'], 'string', 'max' => 32]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'acr_id' => Yii::t('app', 'Acr ID'),
            'acr_name' => Yii::t('app', 'Acr Name'),
            'acr_desc' => Yii::t('app', 'Acr Desc'),
            'acr_create_user' => Yii::t('app', 'Acr Create User'),
            'acr_create_time' => Yii::t('app', 'Acr Create Time'),
            'acr_create_ip' => Yii::t('app', 'Acr Create Ip'),
            'acr_update_user' => Yii::t('app', 'Acr Update User'),
            'acr_update_time' => Yii::t('app', 'Acr Update Time'),
            'acr_update_ip' => Yii::t('app', 'Acr Update Ip'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoleFuncs()
    {
        return $this->hasMany(AcRoleFunc::class, ['acrf_acr_id' => 'acr_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUserRoles()
    {
        return $this->hasMany(AcUserRole::class, ['acur_acr_id' => 'acr_id']);
    }

    /**
     * @inheritdoc
     * @return AcRoleQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new AcRoleQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) return false;
        if ($insert) {
            $this->acr_create_time = date('Y-m-d H:i:s');
            $this->acr_create_user = Yii::$app->user->identity->username;
            $this->acr_create_ip = Yii::$app->request->userIP;
            return true;
        } else {
            $this->acr_update_time = date('Y-m-d H:i:s');
            $this->acr_update_user = Yii::$app->user->identity->username;
            $this->acr_update_ip = Yii::$app->request->userIP;
            return true;
        }
    }

}
