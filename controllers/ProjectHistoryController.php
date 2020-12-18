<?php

namespace app\controllers;

use app\models\Countries;
use app\models\EntityTypes;
use app\models\ProjectHistory;
use app\models\ProjectStages;
use app\models\ProjectStatuses;
use app\models\WorldParts;
use app\repositories\ProjectStatusesRep;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class ProjectHistoryController extends BaseController
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
     * @param int $projectId
     * @return false|string
     * @throws \yii\db\Exception
     */
    public function actionGetAllByProjectId(int $projectId)
    {

        if ($projectId == null){
            $projectId = (int)Yii::$app->request->get('projectId');
        }

        $sql = 'SELECT targetTable.*, ps.status_'.Yii::$app->user->identity->settings['interface_language'].' as status, 
                uc.user_name as user_name_create, uc.user_id as user_name_create_id
                FROM project_history AS targetTable 
                left join project_statuses ps ON (ps.id = targetTable.status_id)
                left join user uc ON (uc.user_id = targetTable.create_user)
                where targetTable.project_id = :projectId
                limit 150
                ';

        $items = Yii::$app->db->createCommand($sql)
            ->bindParam(":projectId",$projectId)
            ->queryAll();

        return json_encode(['items'=> $items]);
    }
}
