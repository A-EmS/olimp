<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "taxes".
 *
 * @property int $id
 * @property string|null $name
 * @property float $tax_part
 */
class Taxes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'taxes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['tax_part'], 'number'],
            [['name'], 'string', 'max' => 1000],
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
            'tax_part' => 'Tax Part',
        ];
    }
}
