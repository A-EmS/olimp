<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "currency_exchange_rates".
 *
 * @property int $id
 * @property int|null $currency_id_base
 * @property int|null $currency_id_ref
 * @property float|null $rate_base
 * @property float|null $rate_ref
 * @property string|null $date_from
 * @property string|null $date_to
 * @property string|null $create_date
 * @property int $update_user
 * @property string|null $update_date
 * @property string|null $notice
 * @property int|null $create_user
 */
class CurrencyExchangeRates extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'currency_exchange_rates';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['currency_id_base', 'currency_id_ref', 'update_user', 'create_user'], 'integer'],
            [['rate_base', 'rate_ref'], 'number'],
            [['date_from', 'date_to', 'create_date', 'update_date'], 'safe'],
            [['notice'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'currency_id_base' => 'Currency Id Base',
            'currency_id_ref' => 'Currency Id Ref',
            'rate_base' => 'Rate Base',
            'rate_ref' => 'Rate Ref',
            'date_from' => 'Date From',
            'date_to' => 'Date To',
            'create_date' => 'Create Date',
            'update_user' => 'Update User',
            'update_date' => 'Update Date',
            'notice' => 'Notice',
            'create_user' => 'Create User',
        ];
    }
}
