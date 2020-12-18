<?php
namespace app\repositories;

use app\models\Entities;
use app\models\ProjectData;
use app\models\Projects;
use app\models\ProjectStatuses;

class ProjectStatusesRep extends ProjectStatuses
{
    public static function existStatus($status_en, $exceptedId = null)
    {
        $item = self::findOne([
            'status_en' => $status_en,
        ]);
        return ($item !== null && $item->id != $exceptedId);
    }
}