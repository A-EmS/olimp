<?php

namespace app\models;

use app\behaviours\NestedSetCustomBehaviour;
use app\queries\FinanceClassesQuery;
use Yii;

/**
 * This is the model class for table "finance_classes".
 *
 * @property int $id
 * @property int $lft
 * @property int $rgt
 * @property int $depth
 * @property string $name
 * @property int $priority
 * @property int $payment_operation_type_id
 * @property string|null $create_date
 * @property int|null $update_user
 * @property string|null $update_date
 * @property int|null $create_user
 */
class FinanceClasses extends \yii\db\ActiveRecord
{
    public function behaviors() {
        return [
            'tree' => [
                'class' => NestedSetCustomBehaviour::className(),
                // 'treeAttribute' => 'tree',
                // 'leftAttribute' => 'lft',
                // 'rightAttribute' => 'rgt',
                // 'depthAttribute' => 'depth',
            ],
        ];
    }

    public function transactions()
    {
        return [
            self::SCENARIO_DEFAULT => self::OP_ALL,
        ];
    }

    public static function find()
    {
        return new FinanceClassesQuery(get_called_class());
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'finance_classes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['lft', 'rgt', 'depth', 'name'], 'required'],
            [['lft', 'rgt', 'depth', 'priority', 'payment_operation_type_id', 'update_user', 'create_user'], 'integer'],
            [['create_date', 'update_date'], 'safe'],
            [['name'], 'string', 'max' => 255],
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
            'lft' => 'Lft',
            'rgt' => 'Rgt',
            'depth' => 'Depth',
            'name' => 'Name',
            'priority' => 'Priority',
            'payment_operation_type_id' => 'Payment Operation Type ID',
            'create_date' => 'Create Date',
            'update_user' => 'Update User',
            'update_date' => 'Update Date',
            'create_user' => 'Create User',
        ];
    }
}
