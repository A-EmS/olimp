<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "banks".
 *
 * @property int $id
 * @property int $country_id
 * @property string $bank_name
 * @property string $bank_code
 * @property string|null $account
 * @property int|null $create_user
 * @property string|null $create_date
 * @property int|null $update_user
 * @property string|null $update_date
 */
class Banks extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'banks';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['country_id', 'bank_name', 'bank_code'], 'required'],
            [['country_id', 'create_user', 'update_user'], 'integer'],
            [['create_date', 'update_date'], 'safe'],
            [['bank_name', 'bank_code', 'account'], 'string', 'max' => 255],
            [['bank_name', 'country_id'], 'unique', 'targetAttribute' => ['bank_name', 'country_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'country_id' => 'Country ID',
            'bank_name' => 'Bank Name',
            'bank_code' => 'Bank Code',
            'account' => 'Account',
            'create_user' => 'Create User',
            'create_date' => 'Create Date',
            'update_user' => 'Update User',
            'update_date' => 'Update Date',
        ];
    }
}
