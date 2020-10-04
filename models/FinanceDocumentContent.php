<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "finance_document_content".
 *
 * @property int $id
 * @property int $document_id
 * @property int $scenario_type
 * @property int|null $contract_id
 * @property int|null $parent_content_id
 * @property float|null $percent
 * @property int|null $product_id
 * @property int|null $service_id
 * @property float $amount
 * @property float|null $cost_without_tax
 * @property float|null $cost_with_tax
 * @property float|null $summ_without_tax
 * @property float|null $summ_with_tax
 * @property float|null $summ_tax
 * @property string|null $notice
 * @property int|null $period_type
 * @property int|null $period_amount
 * @property string|null $start_date
 * @property string|null $end_date
 * @property int|null $create_user
 * @property string|null $create_date
 * @property int $update_user
 * @property string|null $update_date
 */
class FinanceDocumentContent extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'finance_document_content';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['document_id', 'update_user'], 'required'],
            [['document_id', 'scenario_type', 'contract_id', 'parent_content_id', 'product_id', 'service_id', 'period_type', 'period_amount', 'create_user', 'update_user'], 'integer'],
            [['percent', 'amount', 'cost_without_tax', 'cost_with_tax', 'summ_without_tax', 'summ_with_tax', 'summ_tax'], 'number'],
            [['start_date', 'end_date', 'create_date', 'update_date'], 'safe'],
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
            'document_id' => 'Document ID',
            'scenario_type' => 'Scenario Type',
            'contract_id' => 'Contract ID',
            'parent_content_id' => 'Parent Content ID',
            'percent' => 'Percent',
            'product_id' => 'Product ID',
            'service_id' => 'Service ID',
            'amount' => 'Amount',
            'cost_without_tax' => 'Cost Without Tax',
            'cost_with_tax' => 'Cost With Tax',
            'summ_without_tax' => 'Summ Without Tax',
            'summ_with_tax' => 'Summ With Tax',
            'summ_tax' => 'Summ Tax',
            'notice' => 'Notice',
            'period_type' => 'Period Type',
            'period_amount' => 'Period Amount',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'create_user' => 'Create User',
            'create_date' => 'Create Date',
            'update_user' => 'Update User',
            'update_date' => 'Update Date',
        ];
    }
}
