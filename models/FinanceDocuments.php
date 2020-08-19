<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "finance_documents".
 *
 * @property int $id
 * @property string $document_code
 * @property int|null $parent_document_id
 * @property string|null $date
 * @property int $contractor_id
 * @property int $country_id
 * @property int $document_type_id
 * @property int $scenario_type
 * @property int $own_company_id
 * @property int $document_status_id
 * @property int|null $currency_id
 * @property float|null $percent
 * @property string|null $notice
 * @property float|null $summ
 * @property resource|null $template
 * @property string|null $signed_document_scan
 * @property int|null $create_user
 * @property string|null $create_date
 * @property int $update_user
 * @property string|null $update_date
 */
class FinanceDocuments extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'finance_documents';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['document_code', 'document_type_id', 'update_user'], 'required'],
            [['parent_document_id', 'contractor_id', 'country_id', 'document_type_id', 'scenario_type', 'own_company_id', 'document_status_id', 'currency_id', 'create_user', 'update_user'], 'integer'],
            [['date', 'create_date', 'update_date'], 'safe'],
            [['percent', 'summ'], 'number'],
            [['template'], 'string'],
            [['document_code', 'notice', 'signed_document_scan'], 'string', 'max' => 500],
            [['document_type_id', 'own_company_id', 'date', 'document_code'], 'unique', 'targetAttribute' => ['document_type_id', 'own_company_id', 'date', 'document_code']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'document_code' => 'Document Code',
            'parent_document_id' => 'Parent Document ID',
            'date' => 'Date',
            'contractor_id' => 'Contractor ID',
            'country_id' => 'Country ID',
            'document_type_id' => 'Document Type ID',
            'scenario_type' => 'Scenario Type',
            'own_company_id' => 'Own Company ID',
            'document_status_id' => 'Document Status ID',
            'currency_id' => 'Currency ID',
            'percent' => 'Percent',
            'notice' => 'Notice',
            'summ' => 'Summ',
            'template' => 'Template',
            'signed_document_scan' => 'Signed Document Scan',
            'create_user' => 'Create User',
            'create_date' => 'Create Date',
            'update_user' => 'Update User',
            'update_date' => 'Update Date',
        ];
    }
}
