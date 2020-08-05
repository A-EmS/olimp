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
            $wp->amount = Yii::$app->request->post('amount');
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
        $wp->amount = Yii::$app->request->post('amount');
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
