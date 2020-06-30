<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "access_item".
 *
 * @property int $id
 * @property string $name
 * @property string $title_alias
 */
class AccessItem extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'access_item';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'name', 'title_alias'], 'required'],
            [['id'], 'integer'],
            [['name', 'title_alias'], 'string', 'max' => 255],
            [['id'], 'unique'],
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
            'title_alias' => 'Title Alias',
        ];
    }
}
