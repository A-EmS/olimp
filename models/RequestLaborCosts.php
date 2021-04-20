<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "request_labor_costs".
 *
 * @property int $id
 * @property int $request_id
 * @property int $project_part_id
 * @property int $project_stage_id
 * @property int $status
 * @property int $price_list_id
 * @property string $cost_for_day
 * @property string $cost_for_all_days
 * @property string $cost_for_offer
 * @property string $notice
 * @property int $create_user
 * @property string $create_date
 * @property int $update_user
 * @property string $update_date
 * @property string $duration_time_days
 * @property string $project_stage_duration_time_days
 * @property string $extra_charge
 */
class RequestLaborCosts extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'request_labor_costs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['request_id', 'project_part_id', 'project_stage_id', 'status', 'price_list_id', 'create_user', 'update_user'], 'integer'],
            [['cost_for_day', 'cost_for_all_days', 'cost_for_offer', 'duration_time_days', 'project_stage_duration_time_days', 'extra_charge'], 'number'],
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
            'request_id' => 'Request ID',
            'project_part_id' => 'Project Part ID',
            'project_stage_id' => 'Project Stage ID',
            'status' => 'Status',
            'price_list_id' => 'Price List Id',
            'cost_for_day' => 'Cost For Day',
            'cost_for_all_days' => 'Cost For All Days',
            'cost_for_offer' => 'Cost For Offer',
            'notice' => 'Notice',
            'create_user' => 'Create User',
            'create_date' => 'Create Date',
            'update_user' => 'Update User',
            'update_date' => 'Update Date',
            'duration_time_days' => 'Duration Time Days',
            'project_stage_duration_time_days' => 'Project Stage Duration Time Days',
            'extra_charge' => 'Extra Charge',
        ];
    }
}
