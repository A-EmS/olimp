<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "own_companies".
 *
 * @property int $id
 * @property int $entity_id
 * @property int|null $taxes_id
 * @property string|null $notice
 * @property int|null $create_user
 * @property string|null $create_date
 * @property int|null $update_user
 * @property string|null $update_date
 */
class OwnCompanies extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'own_companies';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['entity_id'], 'required'],
            [['entity_id', 'taxes_id', 'create_user', 'update_user'], 'integer'],
            [['notice'], 'string'],
            [['create_date', 'update_date'], 'safe'],
            [['entity_id'], 'unique'],
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
            'taxes_id' => 'Taxes ID',
            'notice' => 'Notice',
            'create_user' => 'Create User',
            'create_date' => 'Create Date',
            'update_user' => 'Update User',
            'update_date' => 'Update Date',
        ];
    }
}
