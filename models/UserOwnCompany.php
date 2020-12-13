<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_own_company".
 *
 * @property int $id
 * @property int $user_id
 * @property int $own_company_id
 */
class UserOwnCompany extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_own_company';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'own_company_id'], 'required'],
            [['user_id', 'own_company_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'own_company_id' => 'Own Company ID',
        ];
    }
}
