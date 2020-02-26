<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "world_parts".
 *
 * @property int $id
 * @property string $name
 * @property int|null $create_user
 * @property string $create_date
 * @property int|null $update_user
 * @property string $update_date
 * @property string|null $notice
 */
class WorldParts extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'world_parts';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['create_user', 'update_user'], 'integer'],
            [['create_date', 'update_date'], 'safe'],
            [['notice'], 'string'],
            [['name'], 'string', 'max' => 255],
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
            'name' => 'Name',
            'create_user' => 'Create User',
            'create_date' => 'Create Date',
            'update_user' => 'Update User',
            'update_date' => 'Update Date',
            'notice' => 'Notice',
        ];
    }
}
