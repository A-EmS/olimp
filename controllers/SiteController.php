<?php

namespace app\controllers;

use app\models\AccessTypes;
use app\models\SentEmailsFromRequest;
use app\models\UserI;
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
                        'allow' => ($_SERVER['HTTP_HOST'] == 'olimp.loc'),
                        'roles' => ['?'],
                    ],
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

    public function actionGetUserMenuConfig()
    {
        if (Yii::$app->user->identity->isAdmin) {
            $sqlAdmin = 'SELECT name
                        FROM access_item
                        ';

            $command = Yii::$app->db->createCommand($sqlAdmin);
            $items = $command->queryColumn();
            return json_encode(['items' => $items]);
        }


        $userId = (int)Yii::$app->user->identity->getId();

        $sqlRoles = 'SELECT acur_acr_id
        FROM ac_user_role
        where acur_user_id = :userId
        ';

        $command = Yii::$app->db->createCommand($sqlRoles);
        $command->bindParam(":userId",$userId);
        $roleIds = $command->queryColumn();

        $sqlRoles = 'SELECT ai.name
        FROM access_grid AS targetTable
        left join access_item ai ON (ai.id = targetTable.access_item_id)
        where targetTable.access_type_id = :accessTypeId AND targetTable.role_id IN ('.implode(',', $roleIds).')
        group by targetTable.access_item_id
        ';

        $accessTypeListId = AccessTypes::ACCESS_TYPE_LIST_ID;
        $command = Yii::$app->db->createCommand($sqlRoles);
        $command->bindParam(":accessTypeId",$accessTypeListId);
        $items = $command->queryColumn();

        return json_encode(['items' => $items]);
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
