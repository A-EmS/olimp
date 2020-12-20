<?php

namespace app\controllers;

use app\models\ProjectData;
use app\models\Projects;
use app\repositories\DocumentTypesRep;
use app\repositories\ProjectsRep;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class ConsolidatedReportController extends BaseController
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


    public function actionGetAllByPage($page = null, $perPage = null, $filters = null)
    {
        $page = (int)Yii::$app->request->post('page', 0);
        $perPage = (int)Yii::$app->request->post('perPage', 50);
        $filters = Yii::$app->request->post('filters', []);

        $whereString = ' targetTable.id > 0 ';
        $havingString = ' ';
        if (!empty($filters)) {
            if (isset($filters['id']) && !empty($filters['id'])) {
                $whereString .= ' AND targetTable.id = '.$filters['id'].' ';
            }

            if (isset($filters['object_crypt']) && !empty($filters['object_crypt'])) {
                $whereString .= ' AND targetTable.object_crypt LIKE \'%'.$filters['object_crypt'].'%\' ';
            }
            if (isset($filters['customer_contractor']) && !empty($filters['customer_contractor'])) {
                $whereString .= ' AND if(ent.short_name is not null, CONCAT(if(et.short_name is not null, et.short_name, ""), " ", ent.short_name), ind.full_name) LIKE \'%'.$filters['customer_contractor'].'%\' ';
            }
            if (isset($filters['payer_contractor']) && !empty($filters['payer_contractor'])) {
                $whereString .= ' AND if(ent_con.short_name is not null, CONCAT(if(et.short_name is not null, et.short_name, ""), " ", ent_con.short_name), ind_con.full_name) LIKE \'%'.$filters['payer_contractor'].'%\' ';
            }
            if (isset($filters['notice']) && !empty($filters['notice'])) {
                $whereString .= ' AND targetTable.notice LIKE \'%'.$filters['notice'].'%\' ';
            }

            //=========================================================
            if (isset($filters['finance_document_date'][0]) && !empty($filters['finance_document_date'][0])) {
                $whereString .= ' AND fd.date >= \''.$filters['finance_document_date'][0].'\' ';
            }
            if (isset($filters['finance_document_date'][1]) && !empty($filters['finance_document_date'][1])) {
                $whereString .= ' AND fd.date <= \''.$filters['finance_document_date'][1].'\' ';
            }

            if (isset($filters['start_date'][0]) && !empty($filters['start_date'][0])) {
                $whereString .= ' AND fdc_info.start_date >= \''.$filters['start_date'][0].'\' ';
            }
            if (isset($filters['start_date'][1]) && !empty($filters['start_date'][1])) {
                $whereString .= ' AND fdc_info.start_date <= \''.$filters['start_date'][1].'\' ';
            }

            if (isset($filters['end_date'][0]) && !empty($filters['end_date'][0])) {
                $whereString .= ' AND fdc_info.end_date >= \''.$filters['end_date'][0].'\' ';
            }
            if (isset($filters['end_date'][1]) && !empty($filters['end_date'][1])) {
                $whereString .= ' AND fdc_info.end_date <= \''.$filters['end_date'][1].'\' ';
            }

            if (isset($filters['act_date'][0]) && !empty($filters['act_date'][0])) {
                $whereString .= ' AND fd_act.date >= \''.$filters['act_date'][0].'\' ';
            }
            if (isset($filters['act_date'][1]) && !empty($filters['act_date'][1])) {
                $whereString .= ' AND fd_act.date <= \''.$filters['act_date'][1].'\' ';
            }
            if (isset($filters['other_services']) && $filters['other_services'] != '') {
                if ($filters['other_services'] == 0) {
                    $havingString = ' HAVING other_services <=1 ';
                 } else {
                    $havingString = ' HAVING other_services >1 ';
                }
            }
            if (isset($filters['payer_manager_individual']) && !empty($filters['payer_manager_individual'])) {
                $pmiInString = implode(',', $filters['payer_manager_individual']);
                $whereString .= ' AND targetTable.payer_manager_individual_id IN ('.$pmiInString.') ';
            }
            if (isset($filters['project_manager_individual']) && !empty($filters['project_manager_individual'])) {
                $pmiInString = implode(',', $filters['project_manager_individual']);
                $whereString .= ' AND targetTable.project_manager_individual_id IN ('.$pmiInString.') ';
            }
            if (isset($filters['performer_own_company']) && !empty($filters['performer_own_company'])) {
                $pocInString = implode(',', $filters['performer_own_company']);
                $whereString .= ' AND targetTable.performer_own_company_id IN ('.$pocInString.') ';
            }
            if (isset($filters['status']) && !empty($filters['status'])) {
                $statusesString = implode(',', $filters['status']);
                $whereString .= ' AND targetTable.status_id IN ('.$statusesString.') ';
            }
        }

        Yii::$app->db->createCommand('SET sql_mode = \'\'')->query();
        $sql = 'SELECT if(count(targetTable.id)>1, "Yes", "No") as other_services, targetTable.*, c.name as country, CONCAT(if(etps.short_name is not null, etps.short_name, ""), " ", e.short_name) as performer_own_company, 
                i_payer.full_name as payer_manager_individual, i_project.full_name as project_manager_individual, fd.date as finance_document_date,
                 fdc_info.start_date, fdc_info.end_date, MAX(fd_act.date) as act_date, ps.status_'.Yii::$app->user->identity->settings['interface_language'].' as status,
                if(ent.short_name is not null, CONCAT(if(et.short_name is not null, et.short_name, ""), " ", ent.short_name), ind.full_name) as customer_contractor, 
                if(ent_con.short_name is not null, CONCAT(if(et.short_name is not null, et.short_name, ""), " ", ent_con.short_name), ind_con.full_name) as payer_contractor
                FROM projects AS targetTable 
                left join countries c ON (c.id = targetTable.country_id)
                left join project_statuses ps ON (ps.id = targetTable.status_id)
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
                
                left join finance_documents fd ON (fd.id = targetTable.finance_document_id)
                left join finance_document_content fdc ON (fdc.document_id = targetTable.finance_document_id)
                left join finance_document_content fdc_info ON (fdc_info.id = targetTable.finance_document_content_id)
                
                left join finance_document_content fdc_act ON (fdc_act.parent_content_id = targetTable.finance_document_content_id and fdc_act.scenario_type=:scenario_act AND (fdc_info.service_id=fdc_act.service_id OR fdc_info.product_id=fdc_act.product_id))
				left join finance_documents fd_act ON (fd_act.id = fdc_act.document_id)
                
                where '.$whereString.'
                group by targetTable.id
                '.$havingString.'
                order by targetTable.id desc
                limit :limit
                offset :offset
                ';

        $act = DocumentTypesRep::SCENARIO_TYPE_ACT;
        $offset = ($page * $perPage) - $perPage;
        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":limit",$perPage);
        $command->bindParam(":offset", $offset);
        $command->bindParam(":scenario_act", $act);
        $items = $command->queryAll();


        $sqlCount = 'SELECT count(*), if(count(targetTable.id)>1, "Yes", "No") as other_services
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
                
                left join finance_documents fd ON (fd.id = targetTable.finance_document_id)
                left join finance_document_content fdc ON (fdc.document_id = targetTable.finance_document_id)
                left join finance_document_content fdc_info ON (fdc_info.id = targetTable.finance_document_content_id)
                
                left join finance_document_content fdc_act ON (fdc_act.parent_content_id = targetTable.finance_document_content_id and fdc_act.scenario_type=:scenario_act AND (fdc_info.service_id=fdc_act.service_id OR fdc_info.product_id=fdc_act.product_id))
				left join finance_documents fd_act ON (fd_act.id = fdc_act.document_id)
                
                where '.$whereString.'
                group by targetTable.id
                '.$havingString.'
                order by targetTable.id desc
                ';


        $commandCount = Yii::$app->db->createCommand($sqlCount);
        $commandCount->bindParam(":scenario_act", $act);
        $count = $commandCount->query();

        return json_encode(['items'=> $items, 'count' => $count->rowCount]);
    }


    public function actionGetPayerManagers()
    {
        $sql = 'SELECT
                i_payer.id as id, i_payer.full_name as name
                FROM projects AS targetTable
                left join individuals i_payer ON (i_payer.id = targetTable.payer_manager_individual_id)
                group by i_payer.id
                ';

        $command = Yii::$app->db->createCommand($sql);
        $items = $command->queryAll();

        return json_encode(['items'=> $items]);
    }

    public function actionGetProjectManagers()
    {
        $sql = 'SELECT
                i_payer.id as id, i_payer.full_name as name
                FROM projects AS targetTable
                left join individuals i_payer ON (i_payer.id = targetTable.project_manager_individual_id)
                group by i_payer.id
                ';

        $command = Yii::$app->db->createCommand($sql);
        $items = $command->queryAll();

        return json_encode(['items'=> $items]);
    }

    public function actionGetPerformers()
    {
        $sql = 'SELECT
                oc.id as id, CONCAT(if(etp.short_name is not null, etp.short_name, ""), " ", e.short_name) as name
                FROM projects AS targetTable
                left join own_companies oc ON (oc.id = targetTable.performer_own_company_id)
                left join entities e ON (e.id = oc.entity_id)
                left join entity_types etp ON (etp.id = e.entity_type_id)
                group by oc.id
                ';

        $command = Yii::$app->db->createCommand($sql);
        $items = $command->queryAll();

        return json_encode(['items'=> $items]);
    }
}
