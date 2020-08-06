<?php

namespace app\controllers;

use app\models\Banks;
use app\models\Cities;
use app\models\Countries;
use app\models\EntityTypes;
use app\models\OwnCompanies;
use app\models\Regions;
use app\models\WorldParts;
use app\repositories\BanksRep;
use app\repositories\DocumentsStatusesRep;
use app\repositories\FinanceActionsRep;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class FinanceBookController extends BaseController
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

        $sql = 'SELECT targetTable.*, c.name as country, c.id as country_id, uc.user_name as user_name_create, uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id 
                FROM finance_book AS targetTable 
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

    public function actionGetInfoByPageAndFilters($page = null, $perPage = null, $filters = null)
    {
        $page = (int)Yii::$app->request->post('page', 0);
        $perPage = (int)Yii::$app->request->post('perPage', 50);
        $filters = Yii::$app->request->post('filters', []);

        $whereString = ' targetTable.id > 0 AND document_status_id = :ds_id ';
        if (!empty($filters)) {
            if (isset($filters['id']) && !empty($filters['id'])) {
                $whereString .= ' AND targetTable.id = '.$filters['id'].' ';
            }
            if (isset($filters['till']) && !empty($filters['till'])) {
                $whereString .= ' AND tls.name LIKE \'%'.$filters['till'].'%\' ';
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
                 targetTable.amount, targetTable.notice, ds.name as document_status, ent.short_name as own_company, fa.name as finance_action, tls.name as till,
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
                
                left join tills tls ON (tls.id = targetTable.till_id)
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
        $paid = DocumentsStatusesRep::STATUS_PAID;
        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":ds_id",$paid);
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
                
                left join tills tls ON (tls.id = targetTable.till_id)
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
        $commandCount->bindParam(":ds_id",$paid);
        $count = $commandCount->queryScalar();

        return json_encode(['items'=> $items, 'count' => $count]);
    }
}
