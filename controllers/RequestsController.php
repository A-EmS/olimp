<?php

namespace app\controllers;

use app\models\Orders;
use app\models\Products;
use app\models\Requests;
use app\repositories\DocumentsStatusesRep;
use app\repositories\FinanceActionsRep;
use app\repositories\PaymentOperationsTypesRep;
use app\repositories\PaymentTypesRep;
use app\repositories\ProductsRep;
use Yii;
use yii\filters\VerbFilter;

class RequestsController extends BaseController
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
    public function actionGetById($id)
    {
        if ($id == null){
            $id = (int)Yii::$app->request->get('id');
        }
        $sql = 'SELECT targetTable.*
                FROM requests as targetTable
                where targetTable.id = :id
                ';

        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":id",$id);
        $items = $command->queryOne();

        return json_encode($items);
    }
//
//    /**
//     * @return false|string
//     * @throws \yii\db\Exception
//     */
//    public function actionGetAll()
//    {
//        $sql = 'SELECT targetTable.*, uc.user_name as user_name_create, uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id
//                FROM orders as targetTable
//                left join user uc ON (uc.user_id = targetTable.create_user)
//                left join user uu ON (uu.user_id = targetTable.update_user)
//                order by targetTable.id desc
//                ';
//
//        $items = Yii::$app->db->createCommand($sql)->queryAll();
//
//        return json_encode(['items'=> $items]);
//    }

//    public function actionGetAllRequests()
//    {
//        $sql = 'SELECT targetTable.id, targetTable.date, targetTable.relatedOrderId, targetTable.report_period, cr.currency_name as currency, pot.name as payment_operation_type, pt.name as payment_type, fc.name as finance_class,
//                 if(e.short_name is not null, CONCAT(if(et.short_name is not null, et.short_name, ""), " ", e.short_name), i.full_name) as contractor,
//                 targetTable.amount, targetTable.notice, ds.name as document_status, ent.short_name as own_company, fa.name as finance_action,
//                 CONCAT(
//                    if(pa.iban is not null && pa.iban!="", CONCAT("iban: ", pa.iban), "") ,
//                    if(pa.account is not null && pa.account!="", CONCAT("account: ", pa.account), "")
//                ) as payment_account,
//                targetTable.create_date, targetTable.update_date,
//                uc.user_name as user_name_create, uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id
//                FROM orders as targetTable
//
//                left join contractor c ON (c.id = targetTable.contractor_id)
//                left join entities e ON (e.id = c.ref_id and c.is_entity = 1)
//                left join entity_types et ON (et.id = e.entity_type_id)
//                left join individuals i ON (i.id = c.ref_id and c.is_entity = 0)
//
//                left join own_companies oc ON (oc.id = targetTable.own_company_id)
//                left join entities ent ON (ent.id = oc.entity_id)
//
//                left join finance_actions fa ON (fa.id = targetTable.finance_action_id)
//                left join payment_accounts pa ON (pa.id = targetTable.payment_account_id)
//                left join documents_statuses ds ON (ds.id = targetTable.document_status_id)
//                left join currencies cr ON (cr.id = targetTable.currency_id)
//                left join finance_classes fc ON (fc.id = targetTable.finance_class_id)
//                left join payment_operation_types pot ON (pot.id = targetTable.payment_operation_type_id)
//                left join payment_types pt ON (pt.id = targetTable.payment_type_id)
//
//                left join user uc ON (uc.user_id = targetTable.create_user)
//                left join user uu ON (uu.user_id = targetTable.update_user)
//                where finance_action_id = :fa_id
//                order by targetTable.id desc
//                ';
//
//        $invoice = FinanceActionsRep::ACTION_CREATE_INVOICE;
//        $command = Yii::$app->db->createCommand($sql);
//        $command->bindParam(":fa_id",$invoice);
//        $items = $command->queryAll();
//
//        return json_encode(['items'=> $items]);
//    }

    public function actionGetAllRequestsByPage($page = null, $perPage = null, $filters = null)
    {
        $page = (int)Yii::$app->request->post('page', 0);
        $perPage = (int)Yii::$app->request->post('perPage', 50);
        $filters = Yii::$app->request->post('filters', []);

        $whereString = ' targetTable.id > 0 ';
        if (!empty($filters)) {
            if (isset($filters['id']) && !empty($filters['id'])) {
                $whereString .= ' AND targetTable.id = '.$filters['id'].' ';
            }

            if (isset($filters['name']) && !empty($filters['name'])) {
                $whereString .= ' AND targetTable.name LIKE \'%'.$filters['name'].'%\' ';
            }
            if (isset($filters['country']) && !empty($filters['country'])) {
                $whereString .= ' AND targetTable.country_id='.$filters['country'].' ';
            }
            if (isset($filters['own_company']) && !empty($filters['own_company'])) {
                $whereString .= ' AND targetTable.own_company_id='.$filters['own_company'].' ';
            }
            if (isset($filters['request_manager_individual']) && !empty($filters['request_manager_individual'])) {
                $whereString .= ' AND i_requester.full_name LIKE \'%'.$filters['request_manager_individual'].'%\' ';
            }
            if (isset($filters['contractor']) && !empty($filters['contractor'])) {
                $whereString .= ' AND (e.short_name LIKE \'%'.$filters['contractor'].'%\' || i.full_name LIKE \'%'.$filters['contractor'].'%\')';
            }
            if (isset($filters['construction_type']) && !empty($filters['construction_type'])) {
                $whereString .= ' AND targetTable.construction_type_id='.$filters['construction_type'].' ';
            }
            if (isset($filters['project_status']) && !empty($filters['project_status'])) {
                $whereString .= ' AND targetTable.project_status_id='.$filters['project_status'].' ';
            }
            if (isset($filters['description']) && !empty($filters['description'])) {
                $whereString .= ' AND targetTable.description LIKE \'%'.$filters['description'].'%\' ';
            }
            if (isset($filters['notice']) && !empty($filters['notice'])) {
                $whereString .= ' AND targetTable.notice LIKE \'%'.$filters['notice'].'%\' ';
            }
            if (isset($filters['date']) && !empty($filters['date'])) {
                $whereString .= ' AND targetTable.date = \''.$filters['date'].'\' ';
            }

            if (isset($filters['user_name_create']) && !empty($filters['user_name_create'])) {
                $whereString .= ' AND uc.user_name LIKE \'%'.$filters['user_name_create'].'%\' ';
            }
            if (isset($filters['create_date']) && !empty($filters['create_date'])) {
                $whereString .= ' AND targetTable.create_date LIKE \'%'.trim($filters['create_date']).'%\' ';
            }
            if (isset($filters['user_name_update']) && !empty($filters['user_name_update'])) {
                $whereString .= ' AND uu.user_name_update LIKE \'%'.$filters['user_name_update'].'%\' ';
            }
            if (isset($filters['update_date']) && !empty($filters['update_date'])) {
                $whereString .= ' AND targetTable.update_date LIKE \'%'.trim($filters['update_date']).'%\' ';
            }
        }

        $sql = 'SELECT targetTable.id, targetTable.date, targetTable.name, targetTable.description, cn.name as country,
                 if(e.short_name is not null, CONCAT(if(et.short_name is not null, et.short_name, ""), " ", e.short_name), i.full_name) as contractor, 
                 targetTable.notice, ent.short_name as own_company,
                ct.name as construction_type,
                ps.status_'.Yii::$app->user->identity->settings['interface_language'].' as project_status,
                i_requester.full_name as request_manager_individual,
                targetTable.create_date, targetTable.update_date,
                uc.user_name as user_name_create, uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id 
                
                FROM requests as targetTable 
                                
                left join countries cn ON (cn.id = targetTable.country_id)
                left join own_companies oc ON (oc.id = targetTable.own_company_id)
                left join entities ent ON (ent.id = oc.entity_id)
                left join individuals i_requester ON (i_requester.id = targetTable.request_manager_individual_id)
                left join construction_types ct ON (ct.id = targetTable.construction_type_id)
                left join project_statuses ps ON (ps.id = targetTable.project_status_id)
                
                left join contractor c ON (c.id = targetTable.contractor_id)
                left join entities e ON (e.id = c.ref_id and c.is_entity = 1)
                left join entity_types et ON (et.id = e.entity_type_id)
                left join individuals i ON (i.id = c.ref_id and c.is_entity = 0)
                
                left join user uc ON (uc.user_id = targetTable.create_user)
                left join user uu ON (uu.user_id = targetTable.update_user)
                where '.$whereString.'
                order by targetTable.id desc
                limit :limit
                offset :offset
                ';

        $offset = ($page * $perPage) - $perPage;
        $invoice = FinanceActionsRep::ACTION_CREATE_INVOICE;
        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":limit",$perPage);
        $command->bindParam(":offset", $offset);
        $items = $command->queryAll();

        $sqlCount = 'SELECT count(*)
                FROM requests as targetTable 
                                
                left join countries cn ON (cn.id = targetTable.country_id)
                left join own_companies oc ON (oc.id = targetTable.own_company_id)
                left join entities ent ON (ent.id = oc.entity_id)
                left join individuals i_requester ON (i_requester.id = targetTable.request_manager_individual_id)
                left join construction_types ct ON (ct.id = targetTable.construction_type_id)
                left join project_statuses ps ON (ps.id = targetTable.project_status_id)
                
                left join contractor c ON (c.id = targetTable.contractor_id)
                left join entities e ON (e.id = c.ref_id and c.is_entity = 1)
                left join entity_types et ON (et.id = e.entity_type_id)
                left join individuals i ON (i.id = c.ref_id and c.is_entity = 0)
                
                left join user uc ON (uc.user_id = targetTable.create_user)
                left join user uu ON (uu.user_id = targetTable.update_user)
                where '.$whereString.'
                ';
        $commandCount = Yii::$app->db->createCommand($sqlCount);
        $commandCount->bindParam(":fa_id",$invoice);
        $count = $commandCount->queryScalar();

        return json_encode(['items'=> $items, 'count' => $count]);
    }

    public function actionCreate()
    {
        try{
            $wp = new Requests();
            $wp->name = Yii::$app->request->post('name');
            $wp->country_id = Yii::$app->request->post('country_id');
            $wp->own_company_id = Yii::$app->request->post('own_company_id');
            $wp->request_manager_individual_id = Yii::$app->request->post('request_manager_individual_id');
            $wp->contractor_id = Yii::$app->request->post('contractor_id');
            $wp->construction_type_id = Yii::$app->request->post('construction_type_id');
            $wp->description = Yii::$app->request->post('description');
            $wp->date = Yii::$app->request->post('date');
            $wp->project_status_id = Yii::$app->request->post('project_status_id');
            $wp->notice = Yii::$app->request->post('notice');

            $wp->create_user = Yii::$app->user->identity->id;
            $wp->create_date = date('Y-m-d H:i:s', time());
            $wp->save(false);

            return $wp->id;
        } catch (\Exception $e){
            return 0;
        }
    }

    public function actionUpdate(int $id = null)
    {
        if ($id == null){
            $id = (int)Yii::$app->request->post('id');
        }

        $wp = Requests::findOne($id);
        $wp->name = Yii::$app->request->post('name');
        $wp->country_id = Yii::$app->request->post('country_id');
        $wp->own_company_id = Yii::$app->request->post('own_company_id');
        $wp->request_manager_individual_id = Yii::$app->request->post('request_manager_individual_id');
        $wp->contractor_id = Yii::$app->request->post('contractor_id');
        $wp->construction_type_id = Yii::$app->request->post('construction_type_id');
        $wp->description = Yii::$app->request->post('description');
        $wp->date = Yii::$app->request->post('date');
        $wp->project_status_id = Yii::$app->request->post('project_status_id');
        $wp->notice = Yii::$app->request->post('notice');

        $wp->update_user = Yii::$app->user->identity->id;
        $wp->update_date = date('Y-m-d H:i:s', time());
        $wp->save(false);
    }

    public function actionDelete(int $id = null) : string
    {
        if ($id == null){
            $id = (int)Yii::$app->request->post('id');
        }

        $wp = Requests::findOne($id);

        if($wp->delete()){
            return json_encode(['status' => true]);
        } else {
            return json_encode(['status' => false]);
        }
    }
}
