<?php
namespace app\repositories;

use app\models\Banks;
use app\models\Contacts;
use Yii;

class BanksRep extends Banks
{
    public static function checkDuplicateByCountryAndCodeAndAccount($countryId, $bankCode, $account, $exceptedId = null)
    {
        $bank = self::findOne(['country_id' => $countryId, 'bank_code' => $bankCode, 'account' => $account]);
        return ($bank !== null && $bank->id != $exceptedId && !empty($bank->account));
    }
}