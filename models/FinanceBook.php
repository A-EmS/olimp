<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "finance_book".
 *
 * @property int $id
 * @property int $order_id
 * @property int|null $own_company_id
 * @property int $payment_operation_type_id
 * @property int $payment_type_id
 * @property int|null $payment_account_id
 * @property int|null $till_id
 * @property int $finance_class_id
 * @property int $contractor_id
 * @property string $date
 * @property string|null $report_period
 * @property int $currency_id
 * @property int $amount
 * @property string|null $notice
 * @property int|null $create_user
 * @property string|null $create_date
 * @property int|null $update_user
 * @property string|null $update_date
 */
class FinanceBook extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'finance_book';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'own_company_id', 'payment_operation_type_id', 'payment_type_id', 'payment_account_id', 'till_id', 'finance_class_id', 'contractor_id', 'currency_id', 'amount', 'create_user', 'update_user'], 'integer'],
            [['date', 'report_period', 'create_date', 'update_date'], 'safe'],
            [['amount'], 'required'],
            [['notice'], 'string', 'max' => 1000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'own_company_id' => 'Own Company ID',
            'payment_operation_type_id' => 'Payment Operation Type ID',
            'payment_type_id' => 'Payment Type ID',
            'payment_account_id' => 'Payment Account ID',
            'till_id' => 'Till ID',
            'finance_class_id' => 'Finance Class ID',
            'contractor_id' => 'Contractor ID',
            'date' => 'Date',
            'report_period' => 'Report Period',
            'currency_id' => 'Currency ID',
            'amount' => 'Amount',
            'notice' => 'Notice',
            'create_user' => 'Create User',
            'create_date' => 'Create Date',
            'update_user' => 'Update User',
            'update_date' => 'Update Date',
        ];
    }
}
