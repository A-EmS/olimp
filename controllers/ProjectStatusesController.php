<?php

namespace app\controllers;

use app\models\Countries;
use app\models\EntityTypes;
use app\models\ProjectStages;
use app\models\ProjectStatuses;
use app\models\WorldParts;
use app\repositories\ProjectStatusesRep;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class ProjectStatusesController extends BaseController
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
    public function actionGetById(int $id)
    {
        if ($id == null){
            $id = (int)Yii::$app->request->get('id');
        }

        $sql = 'SELECT targetTable.*, 
                uc.user_name as user_name_create, uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id 
                FROM project_statuses AS targetTable 
                left join user uc ON (uc.user_id = targetTable.create_user)
                left join user uu ON (uu.user_id = targetTable.update_user)
                where targetTable.id = :id
                ';

        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":id",$id);
        $items = $command->queryOne();

        return json_encode($items);
    }

    /**
     * @return false|string
     * @throws \yii\db\Exception
     */
    public function actionGetAll()
    {

        $sql = 'SELECT targetTable.*, targetTable.status_'.Yii::$app->user->identity->settings['interface_language'].' as status, 
                uc.user_name as user_name_create, uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id 
                FROM project_statuses AS targetTable 
                left join user uc ON (uc.user_id = targetTable.create_user)
                left join user uu ON (uu.user_id = targetTable.update_user)
                ';

        $items = Yii::$app->db->createCommand($sql)->queryAll();

        return json_encode(['items'=> $items]);
    }

    /**
     * @return false|string
     * @throws \yii\db\Exception
     */
    public function actionGetAllByCountryId($countryId)
    {
        if ($countryId == null){
            $countryId = (int)Yii::$app->request->get('countryId');
        }

        $sql = 'SELECT targetTable.*, targetTable.status_'.Yii::$app->user->identity->settings['interface_language'].' as status
                FROM project_statuses AS targetTable 
                where targetTable.country_id = :country_id
                ';

        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":country_id",$countryId);
        $items = $command->queryAll();

        return json_encode(['items'=> $items]);
    }

    public function actionCreate()
    {
        if (ProjectStatusesRep::existStatus(
            Yii::$app->request->post('status_en')
        )
        ){
            return json_encode(['error' => 'Such status is already exist']);
        }

        try{
            $model = new ProjectStatuses();
            $model->status_en = Yii::$app->request->post('status_en');
            $model->status_ru = Yii::$app->request->post('status_ru');

            $model->create_user = Yii::$app->user->identity->id;
            $model->create_date = date('Y-m-d H:i:s', time());
            $model->save(false);

            return $model->id;
        } catch (\Exception $e){
            return json_encode(['error'=> $e->getMessage()]);
        }
    }

    public function actionUpdate(int $id = null)
    {
        if ($id == null){
            $id = (int)Yii::$app->request->post('id');
        }

        if (ProjectStatusesRep::existStatus(
            Yii::$app->request->post('status_en'),
            $id
        )
        ){
            return json_encode(['error' => 'Such status is already exist']);
        }

        $model = ProjectStatuses::findOne($id);
        $model->status_en = Yii::$app->request->post('status_en');
        $model->status_ru = Yii::$app->request->post('status_ru');

        $model->update_user = Yii::$app->user->identity->id;
        $model->update_date = date('Y-m-d H:i:s', time());
        $model->save(false);
    }

    public function actionDelete(int $id = null) : string
    {
        if ($id == null){
            $id = (int)Yii::$app->request->post('id');
        }

        $inProjects = $this->isPresentedIn('projects', 'status_id = '.$id);
        if ($inProjects) return json_encode(['status' => false, 'message' => '']);

        $model = ProjectStatuses::findOne($id);
        if($model->delete()){
            return json_encode(['status' => true]);
        } else {
            return json_encode(['status' => false]);
        }

    }

    public function actionGetAllForSelectByCountryId(int $countryId)
    {
        if ($countryId == null){
            $countryId = (int)Yii::$app->request->get('countryId');
        }

        $sql = 'SELECT ps.id, ps.stage as name
                FROM project_statuses ps
                where ps.country_id = :countryId
                ';

        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":countryId",$countryId);
        $items = $command->queryAll();

        return json_encode(['items'=> $items]);
    }
}
