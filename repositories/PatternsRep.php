<?php
namespace app\repositories;

use app\models\AcRole;
use app\models\Patterns;
use app\models\Tags;
use Yii;

class PatternsRep extends Patterns
{
    public static function checkDuplicate($name, $countryId, $ownCompanyId, $documentTypeId, $exceptedId = null)
    {
        $item = self::findOne(['name' => $name, 'country_id' => $countryId, 'own_company_id' => $ownCompanyId, 'document_type_id' => $documentTypeId, ]);
        return ($item !== null && $item->id != $exceptedId && !empty($item->name));
    }
}