<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "contacts".
 *
 * @property int $id
 * @property int|null $contractor_id
 * @property int|null $contact_type_id
 * @property string|null $name
 * @property string|null $notice
 * @property int|null $create_user
 * @property string|null $create_date
 * @property int|null $update_user
 * @property string|null $update_date
 */
class Contacts extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'contacts';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['contractor_id', 'contact_type_id', 'create_user', 'update_user'], 'integer'],
            [['notice'], 'string'],
            [['create_date', 'update_date'], 'safe'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'contractor_id' => 'Contractor ID',
            'contact_type_id' => 'Contact Type ID',
            'name' => 'Name',
            'notice' => 'Notice',
            'create_user' => 'Create User',
            'create_date' => 'Create Date',
            'update_user' => 'Update User',
            'update_date' => 'Update Date',
        ];
    }
}
