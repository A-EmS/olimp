<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "calendar".
 *
 * @property int $id
 * @property int $country_id
 * @property string|null $date
 * @property int $day_of_week
 * @property int $day_off
 * @property string|null $notice
 * @property int|null $create_user
 * @property string|null $create_date
 * @property int|null $update_user
 * @property string|null $update_date
 */
class Calendar extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'calendar';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['country_id', 'day_of_week', 'day_off', 'create_user', 'update_user'], 'integer'],
            [['date', 'create_date', 'update_date'], 'safe'],
            [['notice'], 'string', 'max' => 1000],
            [['country_id', 'date'], 'unique', 'targetAttribute' => ['country_id', 'date']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'country_id' => 'Country ID',
            'date' => 'Date',
            'day_of_week' => 'Day Of Week',
            'day_off' => 'Day Off',
            'notice' => 'Notice',
            'create_user' => 'Create User',
            'create_date' => 'Create Date',
            'update_user' => 'Update User',
            'update_date' => 'Update Date',
        ];
    }
}
