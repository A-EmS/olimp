<?php
namespace app\repositories;

use app\models\Entities;
use app\models\ProjectContacts;
use app\models\ProjectData;
use app\models\Projects;

class ProjectContactsRep extends ProjectContacts
{
    public static function existByProjectIdNameContractor($project_id, $name, $contractor_id, $exceptedId = null)
    {
        $item = self::findOne([
            'contractor_id' => $contractor_id,
            'name' => $name,
            'project_id' => $project_id,
        ]);
        return ($item !== null && $item->id != $exceptedId);
    }
}