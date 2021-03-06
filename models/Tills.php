<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tills".
 *
 * @property int $id
 * @property string|null $name
 * @property int $currency_id
 * @property int $user_id
 * @property string|null $notice
 * @property int|null $create_user
 * @property string|null $create_date
 * @property int|null $update_user
 * @property string|null $update_date
 */
class Tills extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tills';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['currency_id', 'user_id', 'create_user', 'update_user'], 'integer'],
            [['create_date', 'update_date'], 'safe'],
            [['name'], 'string', 'max' => 255],
            [['notice'], 'string', 'max' => 1000],
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
            'currency_id' => 'Currency ID',
            'user_id' => 'User ID',
            'notice' => 'Notice',
            'create_user' => 'Create User',
            'create_date' => 'Create Date',
            'update_user' => 'Update User',
            'update_date' => 'Update Date',
        ];
    }
}
