<?php
namespace app\repositories;


use app\models\PaymentOperationTypes;
use app\models\PaymentTypes;
use Yii;

class PaymentOperationsTypesRep extends PaymentOperationTypes
{
    const INCOME_ID = 1;
    const EXPENSE_ID = 2;

    const INCOME_TEXT = 'Income';
    const EXPENSE_TEXT = 'Expense';

    const OPERATION_TYPES = [
        self::INCOME_ID => self::INCOME_TEXT,
        self::EXPENSE_ID => self::EXPENSE_TEXT,
    ];

    public static function getNameById($id)
    {
        $model = PaymentOperationTypes::findOne($id);

        if ($model) {
            return $model->name;
        }

        return '';
    }
}