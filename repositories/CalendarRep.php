<?php
namespace app\repositories;

use app\models\Calendar;
use Yii;

class CalendarRep extends Calendar
{
    public static function workDate($date, $countryId, $next = true, $eq = true) {
        if ($next) {
            $order = SORT_ASC;
            if ($eq) {
                $where = '>=';
            } else {
                $where = '>';
            }
        } else {
            $order = SORT_DESC;
            if ($eq) {
                $where = '<=';
            } else {
                $where = '<';
            }
        }
        $cal =
            Calendar::find()
                ->where([$where, 'date', $date])
                ->andWhere(['day_off' => '0'])
                ->andWhere(['country_id' => $countryId])
                ->orderBy(['date' => $order])
                ->one();
        if ($cal) {
            return $cal->date;
        }
        return $date;
    }

    public static function workDatePeriod($date, $count, $countryId, $next = true, $eq = true) {
        if ($next) {
            $order = SORT_ASC;
            if ($eq) {
                $where = '>=';
            } else {
                $where = '>';
            }
        } else {
            $order = SORT_DESC;
            if ($eq) {
                $where = '<=';
            } else {
                $where = '<';
            }
        }
        $cal =
            Calendar::find()
                ->where([$where, 'date', $date])
                ->andWhere(['day_off' => '0'])
                ->andWhere(['country_id' => $countryId])
                ->orderBy(['date' => $order])
                ->limit($count)
                ->all();
        if (!empty($cal)) {
            return $cal[count($cal)-1]->date;
        }
        return $date;
    }

    public static function workDateCheck($date) {
        if (Calendar::find()
            ->where(['date' => $date])
            ->andWhere(['day_off' => '0'])
            ->one()) {
            return true;
        }
        return false;
    }
}