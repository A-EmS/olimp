<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "currencies".
 *
 * @property int $id
 * @property int $country_id
 * @property string $currency_name
 * @property string $currency_short_name
 * @property string|null $sign
 * @property int|null $create_user
 * @property string|null $create_date
 * @property int|null $update_user
 * @property string|null $update_date
 */
class Currencies extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'currencies';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['country_id', 'currency_name', 'currency_short_name'], 'required'],
            [['country_id', 'create_user', 'update_user'], 'integer'],
            [['create_date', 'update_date'], 'safe'],
            [['currency_name', 'currency_short_name'], 'string', 'max' => 255],
            [['sign'], 'string', 'max' => 50],
            [['currency_name', 'country_id'], 'unique', 'targetAttribute' => ['currency_name', 'country_id']],
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
            'currency_name' => 'Currency Name',
            'currency_short_name' => 'Currency Short Name',
            'sign' => 'Sign',
            'create_user' => 'Create User',
            'create_date' => 'Create Date',
            'update_user' => 'Update User',
            'update_date' => 'Update Date',
        ];
    }
}
