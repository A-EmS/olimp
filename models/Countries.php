<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "countries".
 *
 * @property int $id
 * @property string $name
 * @property string|null $full_name
 * @property string|null $phone_code
 * @property string|null $phone_mask
 * @property string|null $alpha2
 * @property string|null $alpha3
 * @property string|null $iso
 * @property int $world_parts_id
 * @property string|null $location
 * @property string|null $flag_code
 * @property int $iban_required
 * @property int $payment_account_required
 * @property int|null $create_user
 * @property string|null $create_date
 * @property int|null $update_user
 * @property string|null $update_date
 */
class Countries extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'countries';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['world_parts_id', 'iban_required', 'payment_account_required', 'create_user', 'update_user'], 'integer'],
            [['location'], 'string'],
            [['create_date', 'update_date'], 'safe'],
            [['name', 'full_name', 'phone_mask', 'iso'], 'string', 'max' => 255],
            [['phone_code', 'alpha2', 'alpha3', 'flag_code'], 'string', 'max' => 10],
            [['name'], 'unique'],
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
            'full_name' => 'Full Name',
            'phone_code' => 'Phone Code',
            'phone_mask' => 'Phone Mask',
            'alpha2' => 'Alpha2',
            'alpha3' => 'Alpha3',
            'iso' => 'Iso',
            'world_parts_id' => 'World Parts ID',
            'location' => 'Location',
            'flag_code' => 'Flag Code',
            'iban_required' => 'Iban Required',
            'payment_account_required' => 'Payment Account Required',
            'create_user' => 'Create User',
            'create_date' => 'Create Date',
            'update_user' => 'Update User',
            'update_date' => 'Update Date',
        ];
    }
}
