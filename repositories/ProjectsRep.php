<?php
namespace app\repositories;

use app\models\Entities;
use app\models\Projects;

class ProjectsRep extends Projects
{
    public static function existByCryptPerformerContract($object_crypt, $performer_own_company_id, $contract_id, $exceptedId = null)
    {
        $item = self::findOne([
            'object_crypt' => $object_crypt,
            'performer_own_company_id' => $performer_own_company_id,
            'contract_id' => $contract_id,
        ]);
        return ($item !== null && $item->id != $exceptedId);
    }
}