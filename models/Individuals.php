<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "individuals".
 *
 * @property int $id
 * @property string $name
 * @property string|null $second_name
 * @property string $third_name
 * @property string $full_name
 * @property int|null $gender
 * @property string|null $birthday
 * @property string|null $inn
 * @property string|null $passport_series
 * @property string|null $passport_number
 * @property string|null $passport_authority
 * @property string|null $passport_authority_date
 * @property string|null $notice
 * @property int|null $create_user
 * @property string|null $create_date
 * @property int|null $update_user
 * @property string|null $update_date
 */
class Individuals extends \yii\db\ActiveRecord
{

    const FEMALE = 0;
    const MALE = 1;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'individuals';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'third_name', 'full_name'], 'required'],
            [['gender', 'create_user', 'update_user'], 'integer'],
            [['birthday', 'passport_authority_date', 'create_date', 'update_date'], 'safe'],
            [['notice'], 'string'],
            [['name', 'second_name', 'third_name', 'full_name', 'inn', 'passport_series', 'passport_number', 'passport_authority'], 'string', 'max' => 255],
            [['inn'], 'unique'],
            [['passport_series', 'passport_number'], 'unique', 'targetAttribute' => ['passport_series', 'passport_number']],
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
            'second_name' => 'Second Name',
            'third_name' => 'Third Name',
            'full_name' => 'Full Name',
            'gender' => 'Gender',
            'birthday' => 'Birthday',
            'inn' => 'Inn',
            'passport_series' => 'Passport Series',
            'passport_number' => 'Passport Number',
            'passport_authority' => 'Passport Authority',
            'passport_authority_date' => 'Passport Authority Date',
            'notice' => 'Notice',
            'create_user' => 'Create User',
            'create_date' => 'Create Date',
            'update_user' => 'Update User',
            'update_date' => 'Update Date',
        ];
    }

    public static function isMale(int $gender){
        return $gender === self::MALE;
    }
}
