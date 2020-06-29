<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "project_data".
 *
 * @property int $id
 * @property int|null $project_id
 * @property int|null $project_stage_id
 * @property int|null $project_part_id
 * @property int|null $performer_contractor_id
 * @property string|null $part_crypt
 * @property string|null $notice
 * @property int|null $create_user
 * @property string|null $create_date
 * @property int|null $update_user
 * @property string|null $update_date
 */
class ProjectData extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project_data';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['project_id', 'project_stage_id', 'project_part_id', 'performer_contractor_id', 'create_user', 'update_user'], 'integer'],
            [['create_date', 'update_date'], 'safe'],
            [['part_crypt'], 'string', 'max' => 255],
            [['notice'], 'string', 'max' => 1000],
            [['part_crypt'], 'unique'],
            [['project_id', 'project_stage_id', 'project_part_id'], 'unique', 'targetAttribute' => ['project_id', 'project_stage_id', 'project_part_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project_id' => 'Project ID',
            'project_stage_id' => 'Project Stage ID',
            'project_part_id' => 'Project Part ID',
            'performer_contractor_id' => 'Performer Contractor ID',
            'part_crypt' => 'Part Crypt',
            'notice' => 'Notice',
            'create_user' => 'Create User',
            'create_date' => 'Create Date',
            'update_user' => 'Update User',
            'update_date' => 'Update Date',
        ];
    }
}
