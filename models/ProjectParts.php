<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "project_parts".
 *
 * @property int $id
 * @property int|null $country_id
 * @property int|null $priority
 * @property int|null $project_stage_id
 * @property string|null $part
 * @property string|null $code
 * @property int|null $create_user
 * @property string|null $create_date
 * @property int|null $update_user
 * @property string|null $update_date
 */
class ProjectParts extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project_parts';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['country_id', 'priority', 'project_stage_id', 'create_user', 'update_user'], 'integer'],
            [['create_date', 'update_date'], 'safe'],
            [['part', 'code'], 'string', 'max' => 255],
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
            'priority' => 'Priority',
            'project_stage_id' => 'Project Stage ID',
            'part' => 'Part',
            'code' => 'Code',
            'create_user' => 'Create User',
            'create_date' => 'Create Date',
            'update_user' => 'Update User',
            'update_date' => 'Update Date',
        ];
    }
}
