<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "payment_accounts".
 *
 * @property int $id
 * @property int $contractor_id
 * @property int $bank_id
 * @property string|null $account
 * @property string|null $iban
 * @property int $currency_id
 * @property int|null $create_user
 * @property string|null $create_date
 * @property int|null $update_user
 * @property string|null $update_date
 */
class PaymentAccounts extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'payment_accounts';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['contractor_id', 'bank_id', 'currency_id'], 'required'],
            [['contractor_id', 'bank_id', 'currency_id', 'create_user', 'update_user'], 'integer'],
            [['create_date', 'update_date'], 'safe'],
            [['account', 'iban'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'contractor_id' => 'Contractor ID',
            'bank_id' => 'Bank ID',
            'account' => 'Account',
            'iban' => 'Iban',
            'currency_id' => 'Currency ID',
            'create_user' => 'Create User',
            'create_date' => 'Create Date',
            'update_user' => 'Update User',
            'update_date' => 'Update Date',
        ];
    }
}
