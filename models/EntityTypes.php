<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "entity_types".
 *
 * @property int $id
 * @property int|null $country_id
 * @property string $name
 * @property string|null $short_name
 * @property string|null $notice
 * @property int|null $create_user
 * @property string|null $create_date
 * @property int|null $update_user
 * @property string|null $update_date
 */
class EntityTypes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'entity_types';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['country_id', 'create_user', 'update_user'], 'integer'],
            [['name'], 'required'],
            [['notice'], 'string'],
            [['create_date', 'update_date'], 'safe'],
            [['name', 'short_name'], 'string', 'max' => 255],
            [['name'], 'unique'],
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
            'short_name' => 'Short Name',
            'notice' => 'Notice',
            'create_user' => 'Create User',
            'create_date' => 'Create Date',
            'update_user' => 'Update User',
            'update_date' => 'Update Date',
        ];
    }
}
