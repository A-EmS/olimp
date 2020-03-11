<?php
namespace app\repositories;

use app\models\PaymentAccounts;
use Yii;

class PaymentAccountsRep extends PaymentAccounts
{
    public static function checkDuplicateByIBANOrAccount($IBAN, $account, $exceptedId = null)
    {
        $row = self::findOne(['iban' => $IBAN]);
        if ($row !== null){
            return ($row !== null && $row->id != $exceptedId && !empty($row->iban));
        }

        $row = self::findOne(['account' => $account]);
        return ($row !== null && $row->id != $exceptedId && !empty($row->account));
    }
}