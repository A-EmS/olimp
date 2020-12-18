<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "project_statuses".
 *
 * @property int $id
 * @property string|null $status_en
 * @property string $status_ru
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
            [['status_ru'], 'required'],
            [['create_user', 'update_user'], 'integer'],
            [['create_date', 'update_date'], 'safe'],
            [['status_en', 'status_ru'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'status_en' => 'Status En',
            'status_ru' => 'Status Ru',
            'create_user' => 'Create User',
            'create_date' => 'Create Date',
            'update_user' => 'Update User',
            'update_date' => 'Update Date',
        ];
    }
}
