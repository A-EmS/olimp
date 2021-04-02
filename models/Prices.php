<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "prices".
 *
 * @property int $id
 * @property int $price_list_id
 * @property int $project_part_id
 * @property string $price
 * @property int $create_user
 * @property string $create_date
 * @property int $update_user
 * @property string $update_date
 */
class Prices extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'prices';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['price_list_id', 'project_part_id', 'create_user', 'update_user'], 'integer'],
            [['price'], 'number'],
            [['create_date', 'update_date'], 'safe'],
            [['price_list_id', 'project_part_id'], 'unique', 'targetAttribute' => ['price_list_id', 'project_part_id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'price_list_id' => 'Price List ID',
            'project_part_id' => 'Project Part ID',
            'price' => 'Price',
            'create_user' => 'Create User',
            'create_date' => 'Create Date',
            'update_user' => 'Update User',
            'update_date' => 'Update Date',
        ];
    }
}
