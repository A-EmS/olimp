<?php
namespace app\repositories;

use app\models\AcRole;
use app\models\DocumentsStatuses;
use app\models\Products;
use app\models\Services;
use Yii;

class DocumentsStatusesRep extends DocumentsStatuses
{
    const STATUS_CREATED = 2;
    const STATUS_PAID = 6;

    public static function checkDuplicateByName($name, $exceptedId = null)
    {
        $item = self::findOne(['name' => $name]);
        return ($item !== null && $item->id != $exceptedId && !empty($item->name));
    }
}