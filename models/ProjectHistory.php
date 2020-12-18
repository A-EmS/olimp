<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "project_history".
 *
 * @property int|null $id
 * @property int|null $project_id
 * @property int|null $status_id
 * @property int|null $create_user
 * @property string|null $create_date
 */
class ProjectHistory extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project_history';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'project_id', 'status_id', 'create_user'], 'integer'],
            [['create_date'], 'safe'],
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
            'status_id' => 'Status ID',
            'create_user' => 'Create User',
            'create_date' => 'Create Date',
        ];
    }
}
