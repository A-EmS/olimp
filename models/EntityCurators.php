<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "entity_curators".
 *
 * @property int $id
 * @property int $entity_id
 * @property int $curator_individual_id
 */
class EntityCurators extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'entity_curators';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['entity_id', 'curator_individual_id'], 'required'],
            [['entity_id', 'curator_individual_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'entity_id' => 'Entity ID',
            'curator_individual_id' => 'Curator Individual ID',
        ];
    }
}
