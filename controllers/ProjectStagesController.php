<?php

namespace app\controllers;

use app\models\Countries;
use app\models\EntityTypes;
use app\models\ProjectStages;
use app\models\WorldParts;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class ProjectStagesController extends BaseController
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

        $sql = 'SELECT targetTable.*, c.name as country, uc.user_name as user_name_create, uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id 
                FROM project_stages AS targetTable 
                left join countries c ON (c.id = targetTable.country_id)
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
        $sql = 'SELECT targetTable.*, c.name as country, uc.user_name as user_name_create, uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id 
                FROM project_stages AS targetTable 
                left join countries c ON (c.id = targetTable.country_id)
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
    public function actionGetAllCodesForSelect()
    {
        $sql = 'SELECT targetTable.code 
                FROM project_stages AS targetTable 
                group by targetTable.code
                ';

        $items = Yii::$app->db->createCommand($sql)->queryAll();

        return json_encode(['items'=> $items]);
    }

    /**
     * @return false|string
     * @throws \yii\db\Exception
     */
    public function actionGetAllCodesForSelectAccordingRequest($requestId)
    {

        if ($requestId == null){
            $requestId = (int)Yii::$app->request->get('requestId');
        }

        $sql = 'SELECT ps.code 
                FROM request_labor_costs AS targetTable
                left join project_stages ps ON (ps.id = targetTable.project_stage_id)
                where targetTable.request_id = :request_id
                group by targetTable.project_stage_id
                ';

        $items = Yii::$app->db->createCommand($sql)->bindParam(":request_id",$requestId)->queryAll();

        return json_encode(['items'=> $items]);
    }


    /**
     * @return false|string
     * @throws \yii\db\Exception
     */
    public function actionGetAllCodesForSelectForProjectParts()
    {

        $sql = 'SELECT ps.code 
                FROM project_parts AS targetTable
                left join project_stages ps ON (ps.id = targetTable.project_stage_id)
                group by ps.code
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

        $sql = 'SELECT targetTable.*, uc.user_name as user_name_create, uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id 
                FROM project_stages AS targetTable 
                left join user uc ON (uc.user_id = targetTable.create_user)
                left join user uu ON (uu.user_id = targetTable.update_user)
                where targetTable.country_id = :country_id
                ';

        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":country_id",$countryId);
        $items = $command->queryAll();

        return json_encode(['items'=> $items]);
    }

    /**
     * @return false|string
     * @throws \yii\db\Exception
     */
    public function actionGetAllCodesForSelectAccordingCountry($countryId)
    {
        if ($countryId == null){
            $countryId = (int)Yii::$app->request->get('countryId');
        }

        $sql = 'SELECT targetTable.code  
                FROM project_stages AS targetTable 
                where targetTable.country_id = :country_id
                ';

        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":country_id",$countryId);
        $items = $command->queryAll();

        return json_encode(['items'=> $items]);
    }

    public function actionCreate()
    {

        try{
            $model = new ProjectStages();
            $model->stage = Yii::$app->request->post('stage');
            $model->code = Yii::$app->request->post('code');
            $model->country_id = Yii::$app->request->post('country_id');

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

        $model = ProjectStages::findOne($id);
        $model->stage = Yii::$app->request->post('stage');
        $model->code = Yii::$app->request->post('code');
        $model->country_id = Yii::$app->request->post('country_id');

        $model->update_user = Yii::$app->user->identity->id;
        $model->update_date = date('Y-m-d H:i:s', time());
        $model->save(false);
    }

    public function actionDelete(int $id = null) : string
    {
        if ($id == null){
            $id = (int)Yii::$app->request->post('id');
        }

        $inProjectParts = $this->isPresentedIn('project_parts', 'project_stage_id = '.$id);
        if ($inProjectParts) return json_encode(['status' => false, 'message' => '']);

        $model = ProjectStages::findOne($id);
        if($model->delete()){
            return json_encode(['status' => true]);
        } else {
            return json_encode(['status' => false]);
        }

    }

    public function actionGetAllForSelect()
    {
        $sql = 'SELECT ps.id, ps.stage as name
                FROM project_stages ps
                ';

        $items = Yii::$app->db->createCommand($sql)->queryAll();

        return json_encode(['items'=> $items]);
    }

    public function actionGetAllForSelectByCountryId(int $countryId)
    {
        if ($countryId == null){
            $countryId = (int)Yii::$app->request->get('countryId');
        }

        $sql = 'SELECT ps.id, ps.stage as name
                FROM project_stages ps
                where ps.country_id = :countryId
                ';

        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":countryId",$countryId);
        $items = $command->queryAll();

        return json_encode(['items'=> $items]);
    }
}
