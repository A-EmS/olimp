<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "orders".
 *
 * @property int $id
 * @property int|null $relatedOrderId
 * @property int|null $till_id
 * @property int $payment_operation_type_id
 * @property int $payment_type_id
 * @property int $finance_class_id
 * @property int $contractor_id
 * @property string $date
 * @property string|null $report_period
 * @property int $currency_id
 * @property float $amount
 * @property int $document_status_id
 * @property int|null $finance_action_id
 * @property string|null $notice
 * @property int|null $base_document_id
 * @property int|null $base_document_content_id
 * @property int|null $own_company_id
 * @property int|null $payment_account_id
 * @property int|null $create_user
 * @property string|null $create_date
 * @property int|null $update_user
 * @property string|null $update_date
 */
class Orders extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orders';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['relatedOrderId', 'till_id', 'payment_operation_type_id', 'payment_type_id', 'finance_class_id', 'contractor_id', 'currency_id', 'document_status_id', 'finance_action_id', 'base_document_id', 'base_document_content_id', 'own_company_id', 'payment_account_id', 'create_user', 'update_user'], 'integer'],
            [['date', 'report_period', 'create_date', 'update_date'], 'safe'],
            [['amount'], 'number'],
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
            'relatedOrderId' => 'Related Order ID',
            'till_id' => 'Till ID',
            'payment_operation_type_id' => 'Payment Operation Type ID',
            'payment_type_id' => 'Payment Type ID',
            'finance_class_id' => 'Finance Class ID',
            'contractor_id' => 'Contractor ID',
            'date' => 'Date',
            'report_period' => 'Report Period',
            'currency_id' => 'Currency ID',
            'amount' => 'Amount',
            'document_status_id' => 'Document Status ID',
            'finance_action_id' => 'Finance Action ID',
            'notice' => 'Notice',
            'base_document_id' => 'Base Document ID',
            'base_document_content_id' => 'Base Document Content ID',
            'own_company_id' => 'Own Company ID',
            'payment_account_id' => 'Payment Account ID',
            'create_user' => 'Create User',
            'create_date' => 'Create Date',
            'update_user' => 'Update User',
            'update_date' => 'Update Date',
        ];
    }
}
