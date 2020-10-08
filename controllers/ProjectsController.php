<?php

namespace app\controllers;

use app\models\ProjectData;
use app\models\Projects;
use app\repositories\ProjectsRep;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class ProjectsController extends BaseController
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
                FROM projects AS targetTable 
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
        $sql = 'SELECT targetTable.*, c.name as country, e.short_name as performer_own_company, 
                i_payer.full_name as payer_manager_individual, i_project.full_name as project_manager_individual,
                if(ent.short_name is not null, CONCAT(if(et.short_name is not null, et.short_name, ""), " ", ent.short_name), ind.full_name) as customer_contractor, 
                if(ent_con.short_name is not null, CONCAT(if(et.short_name is not null, et.short_name, ""), " ", ent_con.short_name), ind_con.full_name) as payer_contractor, 
                uc.user_name as user_name_create, uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id 
                FROM projects AS targetTable 
                left join countries c ON (c.id = targetTable.country_id)
                left join own_companies oc ON (oc.id = targetTable.performer_own_company_id)
                left join entities e ON (e.id = oc.entity_id)
                
                left join contractor ON (contractor.id = targetTable.customer_contractor_id)
                left join entities ent ON (ent.id = contractor.ref_id and contractor.is_entity = 1)
                left join entity_types et ON (et.id = ent.entity_type_id)
                left join individuals ind ON (ind.id = contractor.ref_id and contractor.is_entity = 0)
                
                left join contractor con ON (con.id = targetTable.payer_contractor_id)
                left join entities ent_con ON (ent_con.id = con.ref_id and con.is_entity = 1)
                left join entity_types etp ON (etp.id = ent_con.entity_type_id)
                left join individuals ind_con ON (ind_con.id = con.ref_id and con.is_entity = 0)
                
                left join individuals i_payer ON (i_payer.id = targetTable.payer_manager_individual_id)
                left join individuals i_project ON (i_project.id = targetTable.project_manager_individual_id)
                
                
                left join user uc ON (uc.user_id = targetTable.create_user)
                left join user uu ON (uu.user_id = targetTable.update_user)
                order by targetTable.id desc
                limit 1000
                ';

        $items = Yii::$app->db->createCommand($sql)->queryAll();

        return json_encode(['items'=> $items]);
    }

    public function actionCreate()
    {

        if (trim(Yii::$app->request->post('object_crypt')) != ''){
            if (ProjectsRep::existByCryptPerformerContract(
                Yii::$app->request->post('object_crypt'),
                Yii::$app->request->post('performer_own_company_id')
                )
            ){
                return json_encode(['error' => 'Such combination Object crypt + performer + contract is already exist']);
            }
        }

        try{
            $model = new Projects();
            $model->name = Yii::$app->request->post('name');
            $model->country_id = Yii::$app->request->post('country_id');
            $model->object_crypt = Yii::$app->request->post('object_crypt');
            $model->object_name = Yii::$app->request->post('object_name');
            $model->stamp = Yii::$app->request->post('stamp');
            $model->performer_own_company_id = Yii::$app->request->post('performer_own_company_id');
            $model->customer_contractor_id = Yii::$app->request->post('customer_contractor_id');
            $model->payer_contractor_id = Yii::$app->request->post('payer_contractor_id');
            $model->payer_manager_individual_id = Yii::$app->request->post('payer_manager_individual_id');
            $model->project_manager_individual_id = Yii::$app->request->post('project_manager_individual_id');
            $model->archive = Yii::$app->request->post('archive');
            $model->notice = Yii::$app->request->post('notice');
            $model->finance_document_id = Yii::$app->request->post('finance_document_id');
            $model->finance_document_content_id = Yii::$app->request->post('finance_document_content_id');

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

        if (trim(Yii::$app->request->post('object_crypt')) != ''){
            if (ProjectsRep::existByCryptPerformerContract(
                Yii::$app->request->post('object_crypt'),
                Yii::$app->request->post('performer_own_company_id'),
                $id
            )
            ){
                return json_encode(['error' => 'Such combination Object crypt + performer + contract is already exist']);
            }
        }

        $model = Projects::findOne($id);
        $model->name = Yii::$app->request->post('name');
        $model->country_id = Yii::$app->request->post('country_id');
        $model->object_crypt = Yii::$app->request->post('object_crypt');
        $model->object_name = Yii::$app->request->post('object_name');
        $model->stamp = Yii::$app->request->post('stamp');
        $model->performer_own_company_id = Yii::$app->request->post('performer_own_company_id');
        $model->customer_contractor_id = Yii::$app->request->post('customer_contractor_id');
        $model->payer_contractor_id = Yii::$app->request->post('payer_contractor_id');
        $model->payer_manager_individual_id = Yii::$app->request->post('payer_manager_individual_id');
        $model->project_manager_individual_id = Yii::$app->request->post('project_manager_individual_id');
        $model->archive = Yii::$app->request->post('archive');
        $model->notice = Yii::$app->request->post('notice');
        $model->finance_document_id = Yii::$app->request->post('finance_document_id');
        $model->finance_document_content_id = Yii::$app->request->post('finance_document_content_id');

        $model->update_user = Yii::$app->user->identity->id;
        $model->update_date = date('Y-m-d H:i:s', time());
        $model->save(false);
    }

    public function actionDelete(int $id = null) : string
    {
        if ($id == null){
            $id = (int)Yii::$app->request->post('id');
        }

        $model = Projects::findOne($id);

        if($model->delete()){
            ProjectData::deleteAll(['project_id' => $id]);
            return json_encode(['status' => true]);
        } else {
            return json_encode(['status' => false]);
        }

    }
}
