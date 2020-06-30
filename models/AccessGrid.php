<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "access_grid".
 *
 * @property int $id
 * @property int $role_id
 * @property int $access_item_id
 * @property int $access_type_id
 */
class AccessGrid extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'access_grid';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['role_id', 'access_item_id', 'access_type_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'role_id' => 'Role ID',
            'access_item_id' => 'Access Item ID',
            'access_type_id' => 'Access Type ID',
        ];
    }
}
