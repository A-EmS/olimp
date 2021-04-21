<?php

namespace app\controllers;

use app\models\OwnCompanies;
use app\models\UserOwnCompany;
use app\models\UserSettings;
use app\repositories\UsersPermissionsRep;
use Yii;
use yii\filters\VerbFilter;

class UserSettingsController extends BaseController
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
     * @return false|string
     * @throws \yii\db\Exception
     */
    public function actionGetAllByUserId(int $userId)
    {

        if ($userId == null){
            $userId = (int)Yii::$app->request->get('userId');
        }

        $sql = 'SELECT targetTable.* 
                FROM user_settings AS targetTable 
                where targetTable.user_id = :user_id
                ';

        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":user_id",$userId);
        $items = $command->queryAll();

        return json_encode(['items'=> $items]);
    }

    public function actionChange()
    {
        $data = Yii::$app->request->post('data');

        $model = UserSettings::findOne(['user_id'=>(int)$data['user_id'], 'key'=>$data['key']]);
        if (empty($model)){
            $model = new UserSettings();
            $model->key = $data['key'];
            $model->value = $data['value'];
            $model->user_id = $data['user_id'];
            $model->save(false);
        } else {
            $model->value = $data['value'];
            $model->save(false);
        }
    }
}
