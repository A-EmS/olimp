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

class SiteController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
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
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return \Yii::$app->view->renderFile(Yii::getAlias('@app') . '/web/vuejs/dist/index.html');
    }


    /**
     * @return string
     */
    public function actionUserData()
    {

        if(Yii::$app->user->isGuest){
            return json_encode(false);
        }

        $user = clone Yii::$app->user->identity;
        unset($user->password);
        unset($user->accessActions);

        return json_encode($user);
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        $model = new LoginForm();
        $model->password = Yii::$app->request->post()['password'];
        $model->username = Yii::$app->request->post()['username'];

        if ($model->login()) {
            return $this->actionUserData();
        }

        return json_encode(false);
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();
        return json_encode(false);
    }

    /**
     * Displays setting page.
     *
     * @return string
     */
    public function actionSetting()
    {
        return $this->render('setting');
    }

    /**
     * Displays acl page.
     *
     * @return string
     */
    public function actionAcl()
    {
        return $this->render('acl');
    }
}
