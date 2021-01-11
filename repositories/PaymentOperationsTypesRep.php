<?php
namespace app\repositories;


use app\models\PaymentOperationTypes;
use app\models\PaymentTypes;
use Yii;

class PaymentOperationsTypesRep extends PaymentOperationTypes
{

    public static function getNameById($id)
    {
        $model = PaymentOperationTypes::findOne($id);

        if ($model) {
            return $model->name;
        }

        return '';
    }
}