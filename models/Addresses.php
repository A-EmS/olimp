<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "addresses".
 *
 * @property int $id
 * @property int $contractor_id
 * @property int $address_type_id
 * @property int|null $country_id
 * @property int|null $region_id
 * @property int $city_id
 * @property string|null $index
 * @property string $address
 * @property string|null $notice
 * @property int|null $create_user
 * @property string|null $create_date
 * @property int|null $update_user
 * @property string|null $update_date
 */
class Addresses extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'addresses';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['contractor_id', 'address_type_id', 'address'], 'required'],
            [['contractor_id', 'address_type_id', 'country_id', 'region_id', 'city_id', 'create_user', 'update_user'], 'integer'],
            [['notice'], 'string'],
            [['create_date', 'update_date'], 'safe'],
            [['index', 'address'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'contractor_id' => 'Contractor ID',
            'address_type_id' => 'Address Type ID',
            'country_id' => 'Country ID',
            'region_id' => 'Region ID',
            'city_id' => 'City ID',
            'index' => 'Index',
            'address' => 'Address',
            'notice' => 'Notice',
            'create_user' => 'Create User',
            'create_date' => 'Create Date',
            'update_user' => 'Update User',
            'update_date' => 'Update Date',
        ];
    }
}
