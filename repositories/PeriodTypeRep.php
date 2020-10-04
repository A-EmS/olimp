<?php
namespace app\repositories;

use app\models\DocumentTypes;
use Yii;

class PeriodTypeRep
{
    const WORK_DAYS_TYPE = 0;
    const CALENDAR_DAYS_TYPE = 1;

    const WORK_DAYS_TYPE_TEXT = 'Work days';
    const CALENDAR_DAYS_TYPE_TEXT = 'Calendar days';

    const DAYS_TYPES = [
        self::WORK_DAYS_TYPE => self::WORK_DAYS_TYPE_TEXT,
        self::CALENDAR_DAYS_TYPE => self::CALENDAR_DAYS_TYPE_TEXT,
    ];

    const DAYS_TYPES_IDS = [
        self::WORK_DAYS_TYPE_TEXT => self::WORK_DAYS_TYPE,
        self::CALENDAR_DAYS_TYPE_TEXT => self::CALENDAR_DAYS_TYPE,
    ];
}