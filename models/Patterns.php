<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "patterns".
 *
 * @property int $id
 * @property string $name
 * @property int $own_company_id
 * @property int $country_id
 * @property int $document_type_id
 * @property string $code
 * @property resource $data
 * @property string $notice
 * @property int $create_user
 * @property string $create_date
 * @property int $update_user
 * @property string $update_date
 */
class Patterns extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'patterns';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'own_company_id'], 'required'],
            [['own_company_id', 'country_id', 'document_type_id', 'create_user', 'update_user'], 'integer'],
            [['data'], 'string'],
            [['create_date', 'update_date'], 'safe'],
            [['name'], 'string', 'max' => 128],
            [['code'], 'string', 'max' => 255],
            [['notice'], 'string', 'max' => 512],
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
            'code' => 'Code',
            'own_company_id' => 'Own Company ID',
            'country_id' => 'Country ID',
            'document_type_id' => 'Doc Type ID',
            'data' => 'Data',
            'notice' => 'Notice',
            'create_user' => 'Create User',
            'create_date' => 'Create Date',
            'update_user' => 'Update User',
            'update_date' => 'Update Date',
        ];
    }
}
