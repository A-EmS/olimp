<?php
/**
 * @author Ruslan Bondarenko (Dnipro) r.i.bondarenko@gmail.com
 * @copyright Copyright (C) 2016-2017 Ruslan Bondarenko (Dnipro)
 * @license http://www.yiiframework.com/license/
 */

namespace app\controllers;

use app\models\SentEmailsFromRequest;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class LanguagesController extends BaseController
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
                    'logout' => ['post'],
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
     * @return false|string
     * @throws \yii\db\Exception
     */
    public function actionGetAllLanguages()
    {
        $items = Yii::$app->db->createCommand('SELECT *, acronim as flag FROM languages')->queryAll();

        return json_encode(['items'=> $items]);
    }

    public function actionGetFlags() : string
    {
        $dir = __DIR__.'/../web/img/flags';

        $flags = [];
        foreach (glob($dir.'/*.png') as $img ) {
            $flags[] = ['code' => str_replace('.png','', basename($img))];
        }

        return json_encode($flags);
    }

    public function actionGetFlagByAlpha(string $alfa) : string
    {
        if ($alfa == null){
            $alfa = (int)Yii::$app->request->post('id');
        }

        return json_encode(['flag' => 'en']);
    }

}
