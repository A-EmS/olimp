<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "project_statuses".
 *
 * @property int $id
 * @property int|null $country_id
 * @property string $status
 * @property int|null $create_user
 * @property string|null $create_date
 * @property int|null $update_user
 * @property string|null $update_date
 */
class ProjectStatuses extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project_statuses';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['country_id', 'create_user', 'update_user'], 'integer'],
            [['status'], 'required'],
            [['create_date', 'update_date'], 'safe'],
            [['status'], 'string', 'max' => 255],
            [['country_id', 'status'], 'unique', 'targetAttribute' => ['country_id', 'status']],
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
            'status' => 'Status',
            'create_user' => 'Create User',
            'create_date' => 'Create Date',
            'update_user' => 'Update User',
            'update_date' => 'Update Date',
        ];
    }
}
