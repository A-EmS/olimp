<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "access_types".
 *
 * @property int $id
 * @property string $name
 */
class AccessTypes extends \yii\db\ActiveRecord
{

    const ACCESS_TYPE_LIST_ID = 5;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'access_types';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
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
            'name' => 'Name',
        ];
    }
}
