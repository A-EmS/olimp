<?php
namespace app\repositories;


use app\models\FinanceActions;

class FinanceActionsRep extends FinanceActions
{
    const ACTION_CREATE_INVOICE = 1;
    const ACTION_TILL_OPERATION = 2;
    const ACTION_CURRENCY_EXCHANGE = 3;
    const ACTION_MONEY_TRANSACTION = 4;
}