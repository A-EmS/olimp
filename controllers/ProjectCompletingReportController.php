<?php

namespace app\controllers;

use app\models\ProjectData;
use app\models\Projects;
use app\repositories\ProjectsRep;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class ProjectCompletingReportController extends BaseController
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
    public function actionGetAll()
    {
        $sql = 'SELECT targetTable.*, c.name as country, CONCAT(if(etps.short_name is not null, etps.short_name, ""), " ", e.short_name) as performer_own_company, 
                i_payer.full_name as payer_manager_individual, i_project.full_name as project_manager_individual, fd.date as finance_document_date,
                if(ent.short_name is not null, CONCAT(if(et.short_name is not null, et.short_name, ""), " ", ent.short_name), ind.full_name) as customer_contractor, 
                if(ent_con.short_name is not null, CONCAT(if(et.short_name is not null, et.short_name, ""), " ", ent_con.short_name), ind_con.full_name) as payer_contractor, 
                uc.user_name as user_name_create, uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id 
                FROM projects AS targetTable 
                left join countries c ON (c.id = targetTable.country_id)
                left join own_companies oc ON (oc.id = targetTable.performer_own_company_id)
                left join entities e ON (e.id = oc.entity_id)
                left join entity_types etps ON (etps.id = e.entity_type_id)
                
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
                
                left join finance_documents fd ON (fd.id = targetTable.contract_id)
                
                left join user uc ON (uc.user_id = targetTable.create_user)
                left join user uu ON (uu.user_id = targetTable.update_user)
                order by targetTable.id desc
                limit 1000
                ';

        $items = Yii::$app->db->createCommand($sql)->queryAll();

        return json_encode(['items'=> $items]);
    }

}
