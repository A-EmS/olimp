<?php
namespace app\repositories;

use app\models\Entities;
use app\models\ProjectData;
use app\models\Projects;

class ProjectsDataRep extends ProjectData
{
    public static function existByPartCrypt($part_crypt, $exceptedId = null)
    {
        $item = self::findOne([
            'part_crypt' => $part_crypt,
        ]);

        return ($item !== null && $item->id != $exceptedId);
    }

    public static function existByProjectIdStagePart($project_id, $project_stage_id, $project_part_id, $exceptedId = null)
    {
        $item = self::findOne([
            'project_stage_id' => $project_stage_id,
            'project_part_id' => $project_part_id,
            'project_id' => $project_id,
        ]);
        return ($item !== null && $item->id != $exceptedId);
    }
}