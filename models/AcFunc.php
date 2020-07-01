<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ac_func".
 *
 * @property integer $acf_id
 * @property string $acf_name
 * @property string $acf_controller
 * @property string $acf_action
 * @property string $acf_create_user
 * @property string $acf_create_time
 * @property string $acf_update_user
 * @property string $acf_update_time
 *
 * @property AcRoleFunc[] $RoleFuncs
 */
class AcFunc extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%ac_func}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['acf_name', 'acf_controller', 'acf_action'], 'required'],
            [['acf_create_time', 'acf_update_time'], 'safe'],
            [['acf_name'], 'string', 'max' => 64],
            [['acf_controller', 'acf_action'], 'string', 'max' => 128],
            [['acf_create_user', 'acf_update_user'], 'string', 'max' => 32]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'acf_id' => Yii::t('app', 'Acf ID'),
            'acf_name' => Yii::t('app', 'Acf Name'),
            'acf_controller' => Yii::t('app', 'Acf Controller'),
            'acf_action' => Yii::t('app', 'Acf Action'),
            'acf_create_user' => Yii::t('app', 'Acf Create User'),
            'acf_create_time' => Yii::t('app', 'Acf Create Time'),
            'acf_update_user' => Yii::t('app', 'Acf Update User'),
            'acf_update_time' => Yii::t('app', 'Acf Update Time'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoleFuncs()
    {
        return $this->hasMany(AcRoleFunc::class, ['acrf_acf_id' => 'acf_id']);
    }

    /**
     * @inheritdoc
     * @return AcFuncQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new AcFuncQuery(get_called_class());
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) return false;
        if ($insert) {
            $this->acf_create_time = date('Y-m-d H:i:s');
            $this->acf_create_user = Yii::$app->user->identity->username;
            return true;
        } else {
            $this->acf_update_time = date('Y-m-d H:i:s');
            $this->acf_update_user = Yii::$app->user->identity->username;
            return true;
        }
    }

}
