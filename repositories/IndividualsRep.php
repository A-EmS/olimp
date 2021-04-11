<?php
namespace app\repositories;

use app\models\Contacts;
use app\models\ContactTypes;
use app\models\Contractor;
use app\models\Individuals;

class IndividualsRep extends Individuals
{
    public static function existByINN($INN, $exceptedId = null)
    {
        $item = self::findOne(['inn' => $INN]);
        return ($item !== null && $item->id != $exceptedId);
    }

    public static function existByPassport($number, $series, $exceptedId = null)
    {
        $item = self::findOne(['passport_number' => $number, 'passport_series' => $series]);
        return ($item !== null && $item->id != $exceptedId);
    }

    public function getEmailStringForDocuments() {
        $contractor = Contractor::findOne(['is_entity' => 0, 'ref_id' => $this->id]);

        $contactTypes = [0];
        foreach (ContactTypes::findAll(['input_type' => ContactTypesInputRep::EMAIL_INT_TYPE]) as $item) {
            $contactTypes[] = $item['id'];
        }

        $resultArray = [];
        foreach (Contacts::findAll(['contractor_id' => $contractor->id, 'contact_type_id' => $contactTypes]) as $item) {
            $resultArray[] = $item['name'];
        }

        return implode(', ', $resultArray);
    }

    public function getPhoneStringForDocuments() {
        $contractor = Contractor::findOne(['is_entity' => 0, 'ref_id' => $this->id]);

        $contactTypes = [0];
        foreach (ContactTypes::findAll(['input_type' => ContactTypesInputRep::PHONE_INT_TYPE]) as $item) {
            $contactTypes[] = $item['id'];
        }

        $resultArray = [];
        foreach (Contacts::findAll(['contractor_id' => $contractor->id, 'contact_type_id' => $contactTypes]) as $item) {
            $resultArray[] = $item['name'];
        }

        return implode(', ', $resultArray);
    }
}