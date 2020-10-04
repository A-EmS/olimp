<?php

namespace app\controllers;

use app\models\Banks;
use app\models\Cities;
use app\models\Countries;
use app\models\EntityTypes;
use app\models\OwnCompanies;
use app\models\Regions;
use app\models\WorldParts;
use app\repositories\BanksRep;
use app\repositories\PeriodTypeRep;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class PeriodController extends BaseController
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

    /**
     * @param int $id
     * @return false|string
     * @throws \yii\db\Exception
     */
    public function actionGetTypes()
    {
        $array = [];
        foreach (PeriodTypeRep::DAYS_TYPES as $k => $v) {
            $array[] = ['id'=>$k, 'name'=>$v];
        }

        return json_encode($array);
    }
}
