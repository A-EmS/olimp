<?php

namespace app\controllers;

use app\models\Countries;
use app\models\EntityTypes;
use app\models\ProjectParts;
use app\models\WorldParts;
use app\repositories\PricesRep;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class ProjectPartsController extends BaseController
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

        $sql = 'SELECT targetTable.*, ps.stage as stage, c.name as country, uc.user_name as user_name_create, uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id 
                FROM project_parts AS targetTable 
                left join countries c ON (c.id = targetTable.country_id)
                left join project_stages ps ON (ps.id = targetTable.project_stage_id)
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
        $sql = 'SELECT targetTable.*, ps.stage as stage, ps.code as stage_code, c.name as country, uc.user_name as user_name_create, uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id 
                FROM project_parts AS targetTable 
                left join countries c ON (c.id = targetTable.country_id)
                 left join project_stages ps ON (ps.id = targetTable.project_stage_id)
                left join user uc ON (uc.user_id = targetTable.create_user)
                left join user uu ON (uu.user_id = targetTable.update_user)
                order by targetTable.priority ASC
                ';

        $items = Yii::$app->db->createCommand($sql)->queryAll();

        return json_encode(['items'=> $items]);
    }

    public function actionGetAllByStageId(int $stageId)
    {
        if ($stageId == null){
            $stageId = (int)Yii::$app->request->get('stageId');
        }

        $sql = 'SELECT targetTable.*, ps.stage as stage, c.name as country, uc.user_name as user_name_create, uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id 
                FROM project_parts AS targetTable 
                left join countries c ON (c.id = targetTable.country_id)
                 left join project_stages ps ON (ps.id = targetTable.project_stage_id)
                left join user uc ON (uc.user_id = targetTable.create_user)
                left join user uu ON (uu.user_id = targetTable.update_user)
                where targetTable.project_stage_id = :project_stage_id
                order by targetTable.priority ASC, targetTable.part ASC
                ';

        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":project_stage_id",$stageId);
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
                FROM project_parts AS targetTable 
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
            $model = new ProjectParts();
            $model->part = Yii::$app->request->post('part');
            $model->code = Yii::$app->request->post('code');
            $model->country_id = Yii::$app->request->post('country_id');
            $model->project_stage_id = Yii::$app->request->post('project_stage_id');
            $model->priority = Yii::$app->request->post('priority', 0);

            $model->create_user = Yii::$app->user->identity->id;
            $model->create_date = date('Y-m-d H:i:s', time());
            $model->save(false);

            PricesRep::addProjectPart($model->id);
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

        $model = ProjectParts::findOne($id);
        $model->part = Yii::$app->request->post('part');
        $model->code = Yii::$app->request->post('code');
        $model->country_id = Yii::$app->request->post('country_id');
        $model->project_stage_id = Yii::$app->request->post('project_stage_id');
        $model->priority = Yii::$app->request->post('priority', 0);

        $model->update_user = Yii::$app->user->identity->id;
        $model->update_date = date('Y-m-d H:i:s', time());
        $model->save(false);
    }

    public function actionDelete(int $id = null) : string
    {
        if ($id == null){
            $id = (int)Yii::$app->request->post('id');
        }

        $model = ProjectParts::findOne($id);
        if($model->delete()){
            PricesRep::cleanOutPriceFromProjectPart($id);
            return json_encode(['status' => true]);
        } else {
            return json_encode(['status' => false]);
        }

    }
}
