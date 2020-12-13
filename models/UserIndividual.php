<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "user_individual".
 *
 * @property int $id
 * @property int $user_id
 * @property int $individual_id
 */
class UserIndividual extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'user_individual';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'individual_id'], 'required'],
            [['user_id', 'individual_id'], 'integer'],
            [['user_id'], 'unique'],
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
            'individual_id' => 'Individual ID',
        ];
    }
}
