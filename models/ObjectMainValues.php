<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "object_main_values".
 *
 * @property int $id
 * @property string $name
 * @property string $short_name
 * @property int $country_id
 * @property string $notice
 * @property int $create_user
 * @property string $create_date
 * @property int $update_user
 * @property string $update_date
 */
class ObjectMainValues extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'object_main_values';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'short_name', 'country_id', 'notice'], 'required'],
            [['country_id', 'create_user', 'update_user'], 'integer'],
            [['create_date', 'update_date'], 'safe'],
            [['name', 'short_name'], 'string', 'max' => 255],
            [['notice'], 'string', 'max' => 500],
            [['name', 'country_id'], 'unique', 'targetAttribute' => ['name', 'country_id']],
            [['country_id', 'short_name'], 'unique', 'targetAttribute' => ['country_id', 'short_name']],
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
            'short_name' => 'Short Name',
            'country_id' => 'Country ID',
            'notice' => 'Notice',
            'create_user' => 'Create User',
            'create_date' => 'Create Date',
            'update_user' => 'Update User',
            'update_date' => 'Update Date',
        ];
    }
}
