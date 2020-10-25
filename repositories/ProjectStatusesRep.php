<?php
namespace app\repositories;

use app\models\Entities;
use app\models\ProjectData;
use app\models\Projects;
use app\models\ProjectStatuses;

class ProjectStatusesRep extends ProjectStatuses
{
    public static function existByCountryAndStatus($country_id, $status, $exceptedId = null)
    {
        $item = self::findOne([
            'country_id' => $country_id,
            'status' => $status,
        ]);
        return ($item !== null && $item->id != $exceptedId);
    }
}