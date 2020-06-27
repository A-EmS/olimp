<?php

namespace app\controllers;

use app\models\ProjectData;
use app\repositories\ProjectsDataRep;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class ProjectDataController extends BaseController
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

        $sql = 'SELECT targetTable.*, p.country_id as country_id, uc.user_name as user_name_create, uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id 
                FROM project_data AS targetTable 
                left join projects p ON (p.id = targetTable.project_id)
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
     * @param int $id
     * @return false|string
     * @throws \yii\db\Exception
     */
    public function actionGetAllByProjectId(int $id)
    {
        if ($id == null){
            $id = (int)Yii::$app->request->get('id');
        }

        $sql = 'SELECT targetTable.*, 
                pp.part as project_part, ps.stage as project_stage, 
                uc.user_name as user_name_create, uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id 
                FROM project_data AS targetTable 
                
                left join project_parts pp ON (pp.id = targetTable.project_part_id)
                left join project_stages ps ON (ps.id = targetTable.project_stage_id)
                
                left join user uc ON (uc.user_id = targetTable.create_user)
                left join user uu ON (uu.user_id = targetTable.update_user)
                where targetTable.project_id = :id
                ';

        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":id",$id);
        $items = $command->queryAll();

        return json_encode(['items' => $items]);
    }

    public function actionGetRefreshedCrypt(int $project_id, int $project_part_id)
    {
        if ($project_id == null){
            $project_id = (int)Yii::$app->request->get('project_id');
        }

        if ($project_part_id == null){
            $project_part_id = (int)Yii::$app->request->get('project_part_id');
        }

        $sql = 'SELECT object_crypt
                FROM projects 
                where id = :id
                ';

        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":id",$project_id);
        $items = $command->queryOne();

        $projectCrypt = $items['object_crypt'];

        $sql = 'SELECT code
                FROM project_parts 
                where id = :id
                ';

        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":id",$project_part_id);
        $items = $command->queryOne();

        $partCode = $items['code'];

        return json_encode($projectCrypt.'-'.$partCode);
    }

    public function actionCreate()
    {

        if (ProjectsDataRep::existByPartCrypt(
            trim(Yii::$app->request->post('part_crypt'))
        )
        ){
            return json_encode(['error' => 'Such object crypt is already exist']);
        }

        if (ProjectsDataRep::existByProjectIdStagePart(
            Yii::$app->request->post('project_id'),
            Yii::$app->request->post('project_stage_id'),
            Yii::$app->request->post('project_part_id')
        )
        ){
            return json_encode(['error' => 'Such combination project + stage + part is already exist']);
        }

        try{
            $model = new ProjectData();
            $model->part_crypt = Yii::$app->request->post('part_crypt');
            $model->project_part_id = Yii::$app->request->post('project_part_id');
            $model->project_stage_id = Yii::$app->request->post('project_stage_id');
            $model->project_id = Yii::$app->request->post('project_id');
            $model->notice = Yii::$app->request->post('notice');

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

        $model = ProjectData::findOne($id);
        $model->part_crypt = Yii::$app->request->post('part_crypt');
        $model->project_part_id = Yii::$app->request->post('project_part_id');
        $model->project_stage_id = Yii::$app->request->post('project_stage_id');
        $model->notice = Yii::$app->request->post('notice');

        $model->update_user = Yii::$app->user->identity->id;
        $model->update_date = date('Y-m-d H:i:s', time());
        $model->save(false);
    }

    public function actionDelete(int $id = null) : string
    {
        if ($id == null){
            $id = (int)Yii::$app->request->post('id');
        }

        $model = ProjectData::findOne($id);

        if($model->delete()){
            return json_encode(['status' => true]);
        } else {
            return json_encode(['status' => false]);
        }

    }
}
