<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "contact_types".
 *
 * @property int $id
 * @property string|null $contact_type
 * @property string|null $notice
 * @property int|null $input_type
 * @property int|null $create_user
 * @property string|null $create_date
 * @property int|null $update_user
 * @property string|null $update_date
 */
class ContactTypes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'contact_types';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['notice'], 'string'],
            [['input_type', 'create_user', 'update_user'], 'integer'],
            [['create_date', 'update_date'], 'safe'],
            [['contact_type'], 'string', 'max' => 255],
            [['contact_type'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'contact_type' => 'Contact Type',
            'notice' => 'Notice',
            'input_type' => 'Input Type',
            'create_user' => 'Create User',
            'create_date' => 'Create Date',
            'update_user' => 'Update User',
            'update_date' => 'Update Date',
        ];
    }
}
