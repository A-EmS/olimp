<?php

namespace app\controllers;

use app\models\Calendar;
use app\models\Cities;
use app\models\Countries;
use app\models\EntityTypes;
use app\models\Regions;
use app\models\WorldParts;
use app\repositories\CalendarRep;
use app\repositories\PeriodTypeRep;
use DateInterval;
use DateTime;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class CalendarController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => \yii\filters\AccessControl::class,
                'rules' => [
                    [
                        'allow' => ($_SERVER['HTTP_HOST'] == 'olimp.loc'),
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' =>
                            function ($rule, $action) {
                                return \app\models\AcAccess::checkAction($action);
                            },
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [

                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    public function actionGetItemByDateAndCountry()
    {
        $date = Yii::$app->request->get('date');
        $countryId = (int)Yii::$app->request->get('countryId');

        $sql = 'Select * from calendar 
                    where country_id='.$countryId.' AND date=\''.$date.'\'';

        $command = Yii::$app->db->createCommand($sql);
        $items = $command->queryOne();

        return json_encode($items);
    }


    public function actionUpdateItemById()
    {
        $id = Yii::$app->request->post('id');
        $dayOff = (int)Yii::$app->request->post('day_off');
        $notice = Yii::$app->request->post('notice');

        $wp = Calendar::findOne($id);
        $wp->day_off = $dayOff;
        $wp->notice = $notice;
        $wp->update_user = Yii::$app->user->identity->id;
        $wp->update_date = date('Y-m-d H:i:s', time());
        $wp->save(false);

    }

    public function actionGetByYearAndCountry()
    {
        $year = (int)Yii::$app->request->get('year');
        $countryId = (int)Yii::$app->request->get('countryId');

        $startDate = $year.'-01-01';
        $endDate = $year.'-12-31';

        $sql = 'Select date from calendar 
                    where country_id='.$countryId.' AND day_off=1 AND date>=\''.$startDate.'\' AND date<=\''.$endDate.'\'';

        $daysOff = Calendar::findBySql($sql)->all();

        $items = [];

        foreach ($daysOff as $dayOff) {
            $items[] = ['date' => $dayOff['date'], 'className' => 'red'];
        }

        return json_encode(['items'=> $items]);
    }


    public function actionCalculateDate()
    {
        $startDate = Yii::$app->request->get('startDate');
        $periodAmount = (int)Yii::$app->request->get('periodAmount');
        $periodType = (int)Yii::$app->request->get('periodType');
        $countryId = (int)Yii::$app->request->get('countryId');

        if ($periodType == PeriodTypeRep::CALENDAR_DAYS_TYPE) {
            $date = new DateTime($startDate);
            $interval = new DateInterval('P'.$periodAmount.'D');
            $date->add($interval);
            $endDate = $date->format("Y-m-d");
            return json_encode(['item'=> ['date' => $endDate]]);
        } else if ($periodType == PeriodTypeRep::WORK_DAYS_TYPE) {
            $endDate = CalendarRep::workDatePeriod($startDate, $periodAmount, $countryId);
            return json_encode(['item'=> ['date' => $endDate]]);
        }

        return json_encode(['item'=> ['date' => null]]);
    }

    public function actionGetCountryList()
    {
        return json_encode(['items'=>
            [
                ['id' => 217, 'name' => 'Украина', 'selected' => true],
                ['id' => 171, 'name' => 'Россия'],
            ]
        ]);
    }

    public function actionUpdate()
    {
        $activeDates = Yii::$app->request->post('activeDates', []);
        $disabledDates = Yii::$app->request->post('disabledDates', []);
        $countryId = Yii::$app->request->post('countryId');

        $updateUser = Yii::$app->user->identity->id;
        $updateDate = date('Y-m-d H:i:s', time());

        $ddString = '';
        $adString = '';

        foreach ($disabledDates as $disabledDate) {
            $ddString .= '"'.$disabledDate.'",';
        }

        foreach ($activeDates as $activeDate) {
            $adString .= '"'.$activeDate.'",';
        }

        $ddString = trim($ddString, ',');
        $adString = trim($adString, ',');

        if (!empty($ddString)) {
            $sqlDisableDateUpdate = 'UPDATE calendar SET 
                                    day_off=0, update_user='.$updateUser.', update_date=\''.$updateDate.'\' 
                                    where 
                                    country_id='.$countryId.' AND day_off=1 AND date IN ('.$ddString.')';
            $commandDisableDateUpdate = Yii::$app->db->createCommand($sqlDisableDateUpdate);
            $commandDisableDateUpdate->execute();
        }

        if (!empty($adString)) {
            $sqlActiveDateUpdate = 'UPDATE calendar SET
                                    day_off=1, update_user='.$updateUser.', update_date=\''.$updateDate.'\' 
                                    where 
                                    country_id='.$countryId.' AND day_off=0 AND date IN ('.$adString.')';

            $commandActiveDateUpdate = Yii::$app->db->createCommand($sqlActiveDateUpdate);
            $commandActiveDateUpdate->execute();
        }
    }

    public function actionGenerateForCountry(int $countryId = null)
    {
        if ($countryId == null){
            $countryId = (int)Yii::$app->request->post('countryId');
        }

        $dataIsSet = Calendar::findOne(['country_id' => $countryId]);
        if (!empty($dataIsSet)) {
            return;
        }

        $date = new DateTime('2015-01-01');

        $model = new Calendar();
        $model->country_id = $countryId;
        $model->date = $date->format('Y-m-d');
        $model->day_of_week = $date->format( 'N' );
        if (in_array($model->day_of_week, [6,7])) {
            $model->day_off = 1;
        }
        $model->create_user = Yii::$app->user->identity->id;
        $model->create_date = date('Y-m-d H:i:s', time());
        $model->save(false);

        for ($i = 0; $i<=5477; $i++) {

            $date->modify('+1 day');
            $model = new Calendar();
            $model->country_id = $countryId;
            $model->date = $date->format('Y-m-d');
            $model->day_of_week = $date->format( 'N' );
            if (in_array($model->day_of_week, [6,7])) {
                $model->day_off = 1;
            }
            $model->create_user = Yii::$app->user->identity->id;
            $model->create_date = date('Y-m-d H:i:s', time());
            $model->save(false);
        }

    }
}
