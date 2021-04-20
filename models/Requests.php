<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "requests".
 *
 * @property int $id
 * @property string $name
 * @property int $country_id
 * @property int $own_company_id
 * @property int $request_manager_individual_id
 * @property int $contractor_id
 * @property int $construction_type_id
 * @property string $description
 * @property string $customer_provide
 * @property string $date
 * @property int $project_status_id
 * @property string $notice
 * @property string $file_name
 * @property int $create_user
 * @property string $create_date
 * @property int $update_user
 * @property string $update_date
 */
class Requests extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'requests';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['country_id', 'own_company_id', 'request_manager_individual_id', 'contractor_id', 'construction_type_id', 'project_status_id', 'create_user', 'update_user'], 'integer'],
            [['date', 'create_date', 'update_date'], 'safe'],
            [['name'], 'string', 'max' => 244],
            [['file_name'], 'string'],
            [['description', 'customer_provide', 'notice'], 'string', 'max' => 1000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'country_id' => 'Country ID',
            'own_company_id' => 'Own Company ID',
            'request_manager_individual_id' => 'Request Manager Individual ID',
            'contractor_id' => 'Contractor ID',
            'construction_type_id' => 'Construction Type ID',
            'description' => 'Description',
            'customer_provide' => 'Customer Provide',
            'date' => 'Date',
            'project_status_id' => 'Project Status Id',
            'notice' => 'Notice',
            'file_name' => 'File Name',
            'create_user' => 'Create User',
            'create_date' => 'Create Date',
            'update_user' => 'Update User',
            'update_date' => 'Update Date',
        ];
    }
}
