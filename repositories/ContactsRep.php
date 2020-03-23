<?php
namespace app\repositories;

use app\models\Contacts;
use Yii;

class ContactsRep extends Contacts
{
    public static function checkDuplicateByContactTypeAndName($contactTypeId, $contactName, $exceptedId = null)
    {
        $contact = self::findOne(['contact_type_id' => $contactTypeId, 'name' => $contactName]);
        return ($contact !== null && $contact->contractor_id != $exceptedId);
    }

    public static function checkDuplicateByContractor($contactTypeId, $contractorId, $contactName)
    {
        $contact = self::findOne(['contact_type_id' => $contactTypeId, 'contractor_id' => $contractorId, 'name' => $contactName]);
        return ($contact !== null);
    }

    public function beforeSave($insert)
    {
        if ($this->isNewRecord)
        {
            $this->create_user = Yii::$app->user->identity->id;
            $this->create_date = date('Y-m-d H:i:s', time());
        }

        return parent::beforeSave($insert);
    }
}