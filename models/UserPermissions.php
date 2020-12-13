<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_permissions".
 *
 * @property int $id
 * @property int $user_id
 * @property string $key
 * @property string $label
 * @property int $status
 */
class UserPermissions extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_permissions';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id', 'status'], 'integer'],
            [['key', 'label'], 'string', 'max' => 255],
            [['user_id', 'key'], 'unique', 'targetAttribute' => ['user_id', 'key']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'key' => 'Key',
            'label' => 'Label',
            'status' => 'Status',
        ];
    }
}
