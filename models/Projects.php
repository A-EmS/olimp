<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "projects".
 *
 * @property int $id
 * @property int|null $country_id
 * @property string|null $object_crypt
 * @property string|null $name
 * @property string|null $object_name
 * @property string|null $stamp
 * @property int|null $performer_own_company_id
 * @property int|null $customer_contractor_id
 * @property int|null $payer_contractor_id
 * @property int|null $finance_document_id
 * @property int|null $finance_document_content_id
 * @property int|null $payer_manager_individual_id
 * @property int|null $project_manager_individual_id
 * @property string|null $archive
 * @property string|null $notice
 * @property int|null $create_user
 * @property string|null $create_date
 * @property int|null $update_user
 * @property string|null $update_date
 */
class Projects extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'projects';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['country_id', 'performer_own_company_id', 'customer_contractor_id', 'payer_contractor_id', 'finance_document_id', 'finance_document_content_id', 'payer_manager_individual_id', 'project_manager_individual_id', 'create_user', 'update_user'], 'integer'],
            [['create_date', 'update_date'], 'safe'],
            [['object_crypt', 'name', 'stamp'], 'string', 'max' => 255],
            [['object_name', 'notice'], 'string', 'max' => 1000],
            [['archive'], 'string', 'max' => 500],
            [['object_crypt', 'performer_own_company_id', 'finance_document_id'], 'unique', 'targetAttribute' => ['object_crypt', 'performer_own_company_id', 'finance_document_id']],
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
            'object_crypt' => 'Object Crypt',
            'name' => 'Name',
            'object_name' => 'Object Name',
            'stamp' => 'Stamp',
            'performer_own_company_id' => 'Performer Own Company ID',
            'customer_contractor_id' => 'Customer Contractor ID',
            'payer_contractor_id' => 'Payer Contractor ID',
            'finance_document_id' => 'Finance Document ID',
            'finance_document_content_id' => 'Finance Document Content ID',
            'payer_manager_individual_id' => 'Payer Manager Individual ID',
            'project_manager_individual_id' => 'Project Manager Individual ID',
            'archive' => 'Archive',
            'notice' => 'Notice',
            'create_user' => 'Create User',
            'create_date' => 'Create Date',
            'update_user' => 'Update User',
            'update_date' => 'Update Date',
        ];
    }
}
