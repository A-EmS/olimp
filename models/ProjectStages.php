<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "project_stages".
 *
 * @property int $id
 * @property int|null $country_id
 * @property string $stage
 * @property string|null $code
 * @property int|null $create_user
 * @property string|null $create_date
 * @property int|null $update_user
 * @property string|null $update_date
 */
class ProjectStages extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project_stages';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['country_id', 'create_user', 'update_user'], 'integer'],
            [['stage'], 'required'],
            [['create_date', 'update_date'], 'safe'],
            [['stage', 'code'], 'string', 'max' => 255],
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
            'stage' => 'Stage',
            'code' => 'Code',
            'create_user' => 'Create User',
            'create_date' => 'Create Date',
            'update_user' => 'Update User',
            'update_date' => 'Update Date',
        ];
    }
}
