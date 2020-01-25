<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "interface_vocabularies".
 *
 * @property int $id
 * @property string $lang_en
 * @property string|null $lang_ru
 */
class InterfaceVocabularies extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'interface_vocabularies';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lang_en'], 'required'],
            [['lang_en', 'lang_ru'], 'string', 'max' => 1000],
            [['lang_en'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'lang_en' => 'Lang En',
            'lang_ru' => 'Lang Ru',
        ];
    }
}
