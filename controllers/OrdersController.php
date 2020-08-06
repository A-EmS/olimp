<?php

namespace app\controllers;

use app\models\Orders;
use app\models\Products;
use app\repositories\FinanceActionsRep;
use app\repositories\PaymentTypesRep;
use app\repositories\ProductsRep;
use Yii;
use yii\filters\VerbFilter;

class OrdersController extends BaseController
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
                FROM orders as targetTable
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
        $sql = 'SELECT targetTable.*, uc.user_name as user_name_create, uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id 
                FROM orders as targetTable 
                left join user uc ON (uc.user_id = targetTable.create_user)
                left join user uu ON (uu.user_id = targetTable.update_user)
                order by targetTable.id desc
                ';

        $items = Yii::$app->db->createCommand($sql)->queryAll();

        return json_encode(['items'=> $items]);
    }

    public function actionGetAllInvoices()
    {
        $sql = 'SELECT targetTable.id, targetTable.date, targetTable.report_period, cr.currency_name as currency, pot.name as payment_operation_type, pt.name as payment_type, fc.name as finance_class,
                 if(e.short_name is not null, CONCAT(if(et.short_name is not null, et.short_name, ""), " ", e.short_name), i.full_name) as contractor, 
                 targetTable.amount, targetTable.notice, ds.name as document_status, ent.short_name as own_company, fa.name as finance_action,
                 CONCAT(
                    if(pa.iban is not null && pa.iban!="", CONCAT("iban: ", pa.iban), "") , 
                    if(pa.account is not null && pa.account!="", CONCAT("account: ", pa.account), "")
                ) as payment_account,
                targetTable.create_date, targetTable.update_date,
                uc.user_name as user_name_create, uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id 
                FROM orders as targetTable 
                                
                left join contractor c ON (c.id = targetTable.contractor_id)
                left join entities e ON (e.id = c.ref_id and c.is_entity = 1)
                left join entity_types et ON (et.id = e.entity_type_id)
                left join individuals i ON (i.id = c.ref_id and c.is_entity = 0)
                
                left join own_companies oc ON (oc.id = targetTable.own_company_id)
                left join entities ent ON (ent.id = oc.entity_id)
                
                left join finance_actions fa ON (fa.id = targetTable.finance_action_id)
                left join payment_accounts pa ON (pa.id = targetTable.payment_account_id)
                left join documents_statuses ds ON (ds.id = targetTable.document_status_id)
                left join currencies cr ON (cr.id = targetTable.currency_id)
                left join finance_classes fc ON (fc.id = targetTable.finance_class_id)
                left join payment_operation_types pot ON (pot.id = targetTable.payment_operation_type_id)
                left join payment_types pt ON (pt.id = targetTable.payment_type_id)
                
                left join user uc ON (uc.user_id = targetTable.create_user)
                left join user uu ON (uu.user_id = targetTable.update_user)
                where finance_action_id = :fa_id
                order by targetTable.id desc
                ';

        $invoice = FinanceActionsRep::ACTION_CREATE_INVOICE;
        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":fa_id",$invoice);
        $items = $command->queryAll();

        return json_encode(['items'=> $items]);
    }

    public function actionGetAllInvoicesByPage($page = null, $perPage = null, $filters = null)
    {
        $page = (int)Yii::$app->request->post('page', 0);
        $perPage = (int)Yii::$app->request->post('perPage', 50);
        $filters = Yii::$app->request->post('filters', []);

        $whereString = ' targetTable.id > 0 AND finance_action_id = :fa_id ';
        if (!empty($filters)) {
            if (isset($filters['id']) && !empty($filters['id'])) {
                $whereString .= ' AND targetTable.id = '.$filters['id'].' ';
            }
            if (isset($filters['payment_operation_type_id']) && !empty($filters['payment_operation_type_id'])) {
                $whereString .= ' AND targetTable.payment_operation_type_id='.$filters['payment_operation_type_id'].' ';
            }
            if (isset($filters['payment_type_id']) && !empty($filters['payment_type_id'])) {
                $whereString .= ' AND targetTable.payment_type_id='.$filters['payment_type_id'].' ';
            }
            if (isset($filters['finance_class_id']) && !empty($filters['finance_class_id'])) {
                $whereString .= ' AND targetTable.finance_class_id='.$filters['finance_class_id'].' ';
            }
            if (isset($filters['contractor']) && !empty($filters['contractor'])) {
                $whereString .= ' AND (e.short_name LIKE \'%'.$filters['contractor'].'%\' || i.full_name LIKE \'%'.$filters['contractor'].'%\')';
            }
            if (isset($filters['date']) && !empty($filters['date'])) {
                $whereString .= ' AND targetTable.date = \''.$filters['date'].'\' ';
            }
            if (isset($filters['report_period']) && !empty($filters['report_period'])) {
                $whereString .= ' AND targetTable.report_period = \''.$filters['report_period'].'\' ';
            }
            if (isset($filters['currency_id']) && !empty($filters['currency_id'])) {
                $whereString .= ' AND targetTable.currency_id='.$filters['currency_id'].' ';
            }
            if (isset($filters['amount']) && !empty($filters['amount'])) {
                $whereString .= ' AND targetTable.amount = \''.$filters['amount'].'\' ';
            }
            if (isset($filters['document_status_id']) && !empty($filters['document_status_id'])) {
                $whereString .= ' AND targetTable.document_status_id='.$filters['document_status_id'].' ';
            }
            if (isset($filters['notice']) && !empty($filters['notice'])) {
                $whereString .= ' AND targetTable.notice LIKE \'%'.$filters['notice'].'%\' ';
            }
            if (isset($filters['own_company_id']) && !empty($filters['own_company_id'])) {
                $whereString .= ' AND targetTable.own_company_id='.$filters['own_company_id'].' ';
            }
            if (isset($filters['payment_account']) && !empty($filters['payment_account'])) {
                $whereString .= ' AND (pa.iban LIKE \'%'.trim($filters['payment_account']).'%\' || pa.account LIKE \'%'.trim($filters['payment_account']).'%\') ';
            }
            if (isset($filters['finance_action_id']) && !empty($filters['finance_action_id'])) {
                $whereString .= ' AND targetTable.finance_action_id='.$filters['finance_action_id']. ' ';
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

//        [base_document] =>
//        [base_document_content] =>

        $sql = 'SELECT targetTable.id, targetTable.date, targetTable.report_period, cr.currency_name as currency, pot.name as payment_operation_type, pt.name as payment_type, fc.name as finance_class,
                 if(e.short_name is not null, CONCAT(if(et.short_name is not null, et.short_name, ""), " ", e.short_name), i.full_name) as contractor, 
                 targetTable.amount, targetTable.notice, ds.name as document_status, ent.short_name as own_company, fa.name as finance_action,
                 CONCAT(
                    if(pa.iban is not null && pa.iban!="", CONCAT("iban: ", pa.iban), "") , 
                    if(pa.account is not null && pa.account!="", CONCAT("account: ", pa.account), "")
                ) as payment_account,
                targetTable.create_date, targetTable.update_date,
                uc.user_name as user_name_create, uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id 
                FROM orders as targetTable 
                                
                left join contractor c ON (c.id = targetTable.contractor_id)
                left join entities e ON (e.id = c.ref_id and c.is_entity = 1)
                left join entity_types et ON (et.id = e.entity_type_id)
                left join individuals i ON (i.id = c.ref_id and c.is_entity = 0)
                
                left join own_companies oc ON (oc.id = targetTable.own_company_id)
                left join entities ent ON (ent.id = oc.entity_id)
                
                left join finance_actions fa ON (fa.id = targetTable.finance_action_id)
                left join payment_accounts pa ON (pa.id = targetTable.payment_account_id)
                left join documents_statuses ds ON (ds.id = targetTable.document_status_id)
                left join currencies cr ON (cr.id = targetTable.currency_id)
                left join finance_classes fc ON (fc.id = targetTable.finance_class_id)
                left join payment_operation_types pot ON (pot.id = targetTable.payment_operation_type_id)
                left join payment_types pt ON (pt.id = targetTable.payment_type_id)
                
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
        $command->bindParam(":fa_id",$invoice);
        $command->bindParam(":limit",$perPage);
        $command->bindParam(":offset", $offset);
        $items = $command->queryAll();

        $sqlCount = 'SELECT count(*)
                FROM orders as targetTable 
                                
                left join contractor c ON (c.id = targetTable.contractor_id)
                left join entities e ON (e.id = c.ref_id and c.is_entity = 1)
                left join entity_types et ON (et.id = e.entity_type_id)
                left join individuals i ON (i.id = c.ref_id and c.is_entity = 0)
                
                left join own_companies oc ON (oc.id = targetTable.own_company_id)
                left join entities ent ON (ent.id = oc.entity_id)
                
                left join finance_actions fa ON (fa.id = targetTable.finance_action_id)
                left join payment_accounts pa ON (pa.id = targetTable.payment_account_id)
                left join documents_statuses ds ON (ds.id = targetTable.document_status_id)
                left join currencies cr ON (cr.id = targetTable.currency_id)
                left join finance_classes fc ON (fc.id = targetTable.finance_class_id)
                left join payment_operation_types pot ON (pot.id = targetTable.payment_operation_type_id)
                left join payment_types pt ON (pt.id = targetTable.payment_type_id)
                
                left join user uc ON (uc.user_id = targetTable.create_user)
                left join user uu ON (uu.user_id = targetTable.update_user)
                where '.$whereString.'
                ';
        $commandCount = Yii::$app->db->createCommand($sqlCount);
        $commandCount->bindParam(":fa_id",$invoice);
        $count = $commandCount->queryScalar();

        return json_encode(['items'=> $items, 'count' => $count]);
    }

    public function actionGetAllTillOperationsByPage($page = null, $perPage = null, $filters = null)
    {
        $page = (int)Yii::$app->request->post('page', 0);
        $perPage = (int)Yii::$app->request->post('perPage', 50);
        $filters = Yii::$app->request->post('filters', []);

        $whereString = ' targetTable.id > 0 AND finance_action_id = :fa_id ';
        if (!empty($filters)) {
            if (isset($filters['id']) && !empty($filters['id'])) {
                $whereString .= ' AND targetTable.id = '.$filters['id'].' ';
            }
            if (isset($filters['payment_operation_type_id']) && !empty($filters['payment_operation_type_id'])) {
                $whereString .= ' AND targetTable.payment_operation_type_id='.$filters['payment_operation_type_id'].' ';
            }
            if (isset($filters['payment_type_id']) && !empty($filters['payment_type_id'])) {
                $whereString .= ' AND targetTable.payment_type_id='.$filters['payment_type_id'].' ';
            }
            if (isset($filters['finance_class_id']) && !empty($filters['finance_class_id'])) {
                $whereString .= ' AND targetTable.finance_class_id='.$filters['finance_class_id'].' ';
            }
            if (isset($filters['contractor']) && !empty($filters['contractor'])) {
                $whereString .= ' AND (e.short_name LIKE \'%'.$filters['contractor'].'%\' || i.full_name LIKE \'%'.$filters['contractor'].'%\')';
            }
            if (isset($filters['date']) && !empty($filters['date'])) {
                $whereString .= ' AND targetTable.date = \''.$filters['date'].'\' ';
            }
            if (isset($filters['report_period']) && !empty($filters['report_period'])) {
                $whereString .= ' AND targetTable.report_period = \''.$filters['report_period'].'\' ';
            }
            if (isset($filters['currency_id']) && !empty($filters['currency_id'])) {
                $whereString .= ' AND targetTable.currency_id='.$filters['currency_id'].' ';
            }
            if (isset($filters['amount']) && !empty($filters['amount'])) {
                $whereString .= ' AND targetTable.amount = \''.$filters['amount'].'\' ';
            }
            if (isset($filters['document_status_id']) && !empty($filters['document_status_id'])) {
                $whereString .= ' AND targetTable.document_status_id='.$filters['document_status_id'].' ';
            }
            if (isset($filters['notice']) && !empty($filters['notice'])) {
                $whereString .= ' AND targetTable.notice LIKE \'%'.$filters['notice'].'%\' ';
            }
            if (isset($filters['own_company_id']) && !empty($filters['own_company_id'])) {
                $whereString .= ' AND targetTable.own_company_id='.$filters['own_company_id'].' ';
            }
            if (isset($filters['payment_account']) && !empty($filters['payment_account'])) {
                $whereString .= ' AND (pa.iban LIKE \'%'.trim($filters['payment_account']).'%\' || pa.account LIKE \'%'.trim($filters['payment_account']).'%\') ';
            }
            if (isset($filters['finance_action_id']) && !empty($filters['finance_action_id'])) {
                $whereString .= ' AND targetTable.finance_action_id='.$filters['finance_action_id']. ' ';
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

//        [base_document] =>
//        [base_document_content] =>

        $sql = 'SELECT targetTable.id, targetTable.date, targetTable.report_period, cr.currency_name as currency, pot.name as payment_operation_type, pt.name as payment_type, fc.name as finance_class,
                 if(e.short_name is not null, CONCAT(if(et.short_name is not null, et.short_name, ""), " ", e.short_name), i.full_name) as contractor, 
                 targetTable.amount, targetTable.notice, ds.name as document_status, ent.short_name as own_company, fa.name as finance_action,
                 CONCAT(
                    if(pa.iban is not null && pa.iban!="", CONCAT("iban: ", pa.iban), "") , 
                    if(pa.account is not null && pa.account!="", CONCAT("account: ", pa.account), "")
                ) as payment_account,
                targetTable.create_date, targetTable.update_date,
                uc.user_name as user_name_create, uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id 
                FROM orders as targetTable 
                                
                left join contractor c ON (c.id = targetTable.contractor_id)
                left join entities e ON (e.id = c.ref_id and c.is_entity = 1)
                left join entity_types et ON (et.id = e.entity_type_id)
                left join individuals i ON (i.id = c.ref_id and c.is_entity = 0)
                
                left join own_companies oc ON (oc.id = targetTable.own_company_id)
                left join entities ent ON (ent.id = oc.entity_id)
                
                left join finance_actions fa ON (fa.id = targetTable.finance_action_id)
                left join payment_accounts pa ON (pa.id = targetTable.payment_account_id)
                left join documents_statuses ds ON (ds.id = targetTable.document_status_id)
                left join currencies cr ON (cr.id = targetTable.currency_id)
                left join finance_classes fc ON (fc.id = targetTable.finance_class_id)
                left join payment_operation_types pot ON (pot.id = targetTable.payment_operation_type_id)
                left join payment_types pt ON (pt.id = targetTable.payment_type_id)
                
                left join user uc ON (uc.user_id = targetTable.create_user)
                left join user uu ON (uu.user_id = targetTable.update_user)
                where '.$whereString.'
                order by targetTable.id desc
                limit :limit
                offset :offset
                ';

        $offset = ($page * $perPage) - $perPage;
        $invoice = FinanceActionsRep::ACTION_TILL_OPERATION;
        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":fa_id",$invoice);
        $command->bindParam(":limit",$perPage);
        $command->bindParam(":offset", $offset);
        $items = $command->queryAll();

        $sqlCount = 'SELECT count(*)
                FROM orders as targetTable 
                                
                left join contractor c ON (c.id = targetTable.contractor_id)
                left join entities e ON (e.id = c.ref_id and c.is_entity = 1)
                left join entity_types et ON (et.id = e.entity_type_id)
                left join individuals i ON (i.id = c.ref_id and c.is_entity = 0)
                
                left join own_companies oc ON (oc.id = targetTable.own_company_id)
                left join entities ent ON (ent.id = oc.entity_id)
                
                left join finance_actions fa ON (fa.id = targetTable.finance_action_id)
                left join payment_accounts pa ON (pa.id = targetTable.payment_account_id)
                left join documents_statuses ds ON (ds.id = targetTable.document_status_id)
                left join currencies cr ON (cr.id = targetTable.currency_id)
                left join finance_classes fc ON (fc.id = targetTable.finance_class_id)
                left join payment_operation_types pot ON (pot.id = targetTable.payment_operation_type_id)
                left join payment_types pt ON (pt.id = targetTable.payment_type_id)
                
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
            $wp = new Orders();
            $wp->payment_operation_type_id = Yii::$app->request->post('payment_operation_type_id');
            $wp->payment_type_id = Yii::$app->request->post('payment_type_id');
            $wp->finance_class_id = Yii::$app->request->post('finance_class_id');
            $wp->contractor_id = Yii::$app->request->post('contractor_id');
            $wp->date = Yii::$app->request->post('date');
            $wp->report_period = Yii::$app->request->post('report_period');
            $wp->currency_id = Yii::$app->request->post('currency_id');
            $wp->amount = (float)Yii::$app->request->post('amount');
            $wp->document_status_id = Yii::$app->request->post('document_status_id');
            $wp->notice = Yii::$app->request->post('notice');
            $wp->base_document_id = Yii::$app->request->post('base_document_id');
            $wp->base_document_content_id = Yii::$app->request->post('base_document_content_id');
            $wp->own_company_id = Yii::$app->request->post('own_company_id');
            $wp->payment_account_id = Yii::$app->request->post('payment_account_id');
            $wp->finance_action_id = Yii::$app->request->post('finance_action_id');


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

        $wp = Orders::findOne($id);
        $wp->payment_operation_type_id = Yii::$app->request->post('payment_operation_type_id');
        $wp->payment_type_id = Yii::$app->request->post('payment_type_id');
        $wp->finance_class_id = Yii::$app->request->post('finance_class_id');
        $wp->contractor_id = Yii::$app->request->post('contractor_id');
        $wp->date = Yii::$app->request->post('date');
        $wp->report_period = Yii::$app->request->post('report_period');
        $wp->currency_id = Yii::$app->request->post('currency_id');
        $wp->amount = (float)Yii::$app->request->post('amount');
        $wp->document_status_id = Yii::$app->request->post('document_status_id');
        $wp->notice = Yii::$app->request->post('notice');
        $wp->base_document_id = Yii::$app->request->post('base_document_id');
        $wp->base_document_content_id = Yii::$app->request->post('base_document_content_id');
        $wp->own_company_id = Yii::$app->request->post('own_company_id');
        $wp->payment_account_id = Yii::$app->request->post('payment_account_id');
        $wp->finance_action_id = Yii::$app->request->post('finance_action_id');

        $wp->update_user = Yii::$app->user->identity->id;
        $wp->update_date = date('Y-m-d H:i:s', time());
        $wp->save(false);
    }

    public function actionDelete(int $id = null) : string
    {
        if ($id == null){
            $id = (int)Yii::$app->request->post('id');
        }

        $wp = Orders::findOne($id);
        if($wp->delete()){
            return json_encode(['status' => true]);
        } else {
            return json_encode(['status' => false]);
        }
    }
}
