<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "contractor".
 *
 * @property int $id
 * @property string $ref_id
 * @property int|null $is_entity
 * @property int $individual_id_manager
 */
class Contractor extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'contractor';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['ref_id'], 'required'],
            [['is_entity', 'individual_id_manager'], 'integer'],
            [['ref_id'], 'string', 'max' => 255],
            [['ref_id', 'is_entity'], 'unique', 'targetAttribute' => ['ref_id', 'is_entity']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'ref_id' => 'Ref ID',
            'is_entity' => 'Is Entity',
            'individual_id_manager' => 'Individual Id Manager',
        ];
    }
}
