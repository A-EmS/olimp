<?php
namespace app\repositories;


use app\models\PaymentTypes;

class PaymentTypesRep extends PaymentTypes
{
    const PAYMENT_TYPE_CASH = 1;
    const PAYMENT_TYPE_CASHLESS = 2;
}