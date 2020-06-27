<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ac_role".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 * @property int|null $create_user
 * @property string|null $create_date
 * @property int|null $update_user
 * @property string|null $update_date
 *
 * @property AcRoleFunc[] $acRoleFuncs
 * @property AcUserRole[] $acUserRoles
 */
class AcRole extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ac_role';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'description'], 'required'],
            [['create_user', 'update_user'], 'integer'],
            [['create_date', 'update_date'], 'safe'],
            [['name'], 'string', 'max' => 64],
            [['description'], 'string', 'max' => 256],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'description' => 'Description',
            'create_user' => 'Create User',
            'create_date' => 'Create Date',
            'update_user' => 'Update User',
            'update_date' => 'Update Date',
        ];
    }

//    /**
//     * @return \yii\db\ActiveQuery
//     */
//    public function getAcRoleFuncs()
//    {
//        return $this->hasMany(AcRoleFunc::className(), ['acrf_acr_id' => 'id']);
//    }
//
//    /**
//     * @return \yii\db\ActiveQuery
//     */
//    public function getAcUserRoles()
//    {
//        return $this->hasMany(AcUserRole::className(), ['acur_acr_id' => 'id']);
//    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) return false;
        if ($insert) {
            $this->create_date = date('Y-m-d H:i:s');
            $this->create_user = Yii::$app->user->identity->id;
            return true;
        } else {
            $this->update_date = date('Y-m-d H:i:s');
            $this->update_user = Yii::$app->user->identity->id;
            return true;
        }
    }
}
