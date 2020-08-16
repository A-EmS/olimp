<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "finance_document_content".
 *
 * @property int $id
 * @property int $document_id
 * @property int $parent_content_id
 * @property float|null $percent
 * @property int $product_id
 * @property int $service_id
 * @property float $amount
 * @property float $cost_without_tax
 * @property float $cost_with_tax
 * @property float $summ_without_tax
 * @property float $summ_with_tax
 * @property float $summ_tax
 * @property string|null $notice
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
            [['document_id', 'parent_content_id', 'update_user'], 'required'],
            [['document_id', 'parent_content_id', 'product_id', 'service_id', 'create_user', 'update_user'], 'integer'],
            [['percent', 'amount', 'cost_without_tax', 'cost_with_tax', 'summ_without_tax', 'summ_with_tax', 'summ_tax'], 'number'],
            [['create_date', 'update_date'], 'safe'],
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
            'create_user' => 'Create User',
            'create_date' => 'Create Date',
            'update_user' => 'Update User',
            'update_date' => 'Update Date',
        ];
    }
}
