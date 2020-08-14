<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "document_types".
 *
 * @property int $id
 * @property int $country_id
 * @property string $name
 * @property string|null $notice
 * @property int $priority
 * @property int $scenario_type
 * @property int|null $create_user
 * @property string|null $create_date
 * @property int|null $update_user
 * @property string|null $update_date
 */
class DocumentTypes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'document_types';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['country_id', 'priority', 'scenario_type', 'create_user', 'update_user'], 'integer'],
            [['name'], 'required'],
            [['notice'], 'string'],
            [['create_date', 'update_date'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['country_id', 'name'], 'unique', 'targetAttribute' => ['country_id', 'name']],
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
            'name' => 'Name',
            'notice' => 'Notice',
            'priority' => 'Priority',
            'scenario_type' => 'Scenario Type',
            'create_user' => 'Create User',
            'create_date' => 'Create Date',
            'update_user' => 'Update User',
            'update_date' => 'Update Date',
        ];
    }
}
