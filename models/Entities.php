<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "entities".
 *
 * @property int $id
 * @property int|null $entity_type_id
 * @property string $name
 * @property string $short_name
 * @property string|null $ogrn
 * @property string|null $inn
 * @property string|null $kpp
 * @property string|null $okpo
 * @property string|null $notice
 * @property int|null $create_user
 * @property string|null $create_date
 * @property int|null $update_user
 * @property string|null $update_date
 */
class Entities extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'entities';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['entity_type_id', 'create_user', 'update_user'], 'integer'],
            [['name', 'short_name'], 'required'],
            [['notice'], 'string'],
            [['create_date', 'update_date'], 'safe'],
            [['name', 'short_name', 'ogrn', 'inn', 'kpp', 'okpo'], 'string', 'max' => 255],
            [['inn'], 'unique'],
            [['ogrn'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'entity_type_id' => 'Entity Type ID',
            'name' => 'Name',
            'short_name' => 'Short Name',
            'ogrn' => 'Ogrn',
            'inn' => 'Inn',
            'kpp' => 'Kpp',
            'okpo' => 'Okpo',
            'notice' => 'Notice',
            'create_user' => 'Create User',
            'create_date' => 'Create Date',
            'update_user' => 'Update User',
            'update_date' => 'Update Date',
        ];
    }
}
