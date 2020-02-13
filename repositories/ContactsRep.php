<?php
namespace app\repositories;

use app\models\Contacts;

class ContactsRep extends Contacts
{
    public static function checkDuplicateByContactTypeAndName($contactTypeId, $contactName)
    {
        $contact = self::findOne(['contact_type_id' => $contactTypeId, 'name' => $contactName]);
        return ($contact !== null);
    }

    public static function checkDuplicateByContractor($contactTypeId, $contractorId, $contactName)
    {
        $contact = self::findOne(['contact_type_id' => $contactTypeId, 'contractor_id' => $contractorId, 'name' => $contactName]);
        return ($contact !== null);
    }
}