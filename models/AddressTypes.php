<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "address_types".
 *
 * @property int $id
 * @property string|null $address_type
 * @property string|null $notice
 * @property int|null $create_user
 * @property string|null $create_date
 * @property int|null $update_user
 * @property string|null $update_date
 */
class AddressTypes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'address_types';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['notice'], 'string'],
            [['create_user', 'update_user'], 'integer'],
            [['create_date', 'update_date'], 'safe'],
            [['address_type'], 'string', 'max' => 255],
            [['address_type'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'address_type' => 'Address Type',
            'notice' => 'Notice',
            'create_user' => 'Create User',
            'create_date' => 'Create Date',
            'update_user' => 'Update User',
            'update_date' => 'Update Date',
        ];
    }
}
