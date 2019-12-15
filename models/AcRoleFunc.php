<?php
/**
 * @author Ruslan Bondarenko (Dnipro) r.i.bondarenko@gmail.com
 * @copyright Copyright (C) 2016-2017 Ruslan Bondarenko (Dnipro)
 * @license http://www.yiiframework.com/license/
 */

namespace app\models;

use Yii;

/**
 * This is the model class for table "ac_role_func".
 *
 * @property integer $acrf_id
 * @property integer $acrf_acr_id
 * @property integer $acrf_acf_id
 * @property string $acrf_create_user
 * @property string $acrf_create_time
 * @property string $acrf_update_user
 * @property string $acrf_update_time
 *
 * @property AcRole $role
 * @property AcFunc $func
 */
class AcRoleFunc extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%ac_role_func}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['acrf_acr_id', 'acrf_acf_id'], 'required'],
            [['acrf_acr_id', 'acrf_acf_id'], 'integer'],
            [['acrf_create_time', 'acrf_update_time'], 'safe'],
            [['acrf_create_user', 'acrf_update_user'], 'string', 'max' => 32]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'acrf_id' => Yii::t('app', 'Acrf ID'),
            'acrf_acr_id' => Yii::t('app', 'Acrf Acr ID'),
            'acrf_acf_id' => Yii::t('app', 'Acrf Acf ID'),
            'acrf_create_user' => Yii::t('app', 'Acrf Create User'),
            'acrf_create_time' => Yii::t('app', 'Acrf Create Time'),
            'acrf_update_user' => Yii::t('app', 'Acrf Update User'),
            'acrf_update_time' => Yii::t('app', 'Acrf Update Time'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(AcRole::class, ['acr_id' => 'acrf_acr_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFunc()
    {
        return $this->hasOne(AcFunc::class, ['acf_id' => 'acrf_acf_id']);
    }

    /**
     * @inheritdoc
     * @return AcRoleFuncQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new AcRoleFuncQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) return false;
        if ($insert) {
            $this->acrf_create_time = date('Y-m-d H:i:s');
            $this->acrf_create_user = Yii::$app->user->identity->username;
            return true;
        } else {
            $this->acrf_update_time = date('Y-m-d H:i:s');
            $this->acrf_update_user = Yii::$app->user->identity->username;
            return true;
        }
    }

    /**
     * Check for exists rule.
     * @param integer $acr_id
     * @param integer $acf_id
     * @return boolean
     */
    public static function exists($acr_id, $acf_id)
    {
        return AcRoleFunc::find()->where(['acrf_acr_id' => $acr_id, 'acrf_acf_id' => $acf_id, ])->exists();
    }

    /**
     * Check for exists rule.
     * @param integer $acr_id
     * @param integer $acf_id
     * @return AcRoleFunc()
     */
    public static function existsOne($acr_id, $acf_id)
    {
        if (($model = AcRoleFunc::find()->where(['acrf_acr_id' => $acr_id, 'acrf_acf_id' => $acf_id, ])->one()) === null) {
            return false;
        }
        return $model;
    }

}
