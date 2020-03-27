<?php

namespace app\controllers;

use app\models\PaymentAccounts;
use app\repositories\PaymentAccountsRep;
use Yii;
use yii\filters\VerbFilter;

class PaymentAccountsController extends BaseController
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

        $sql = 'SELECT targetTable.*, cr.id as currency_id, b.id as bank_id, b.country_id, c.id as contractor_id, uc.user_name as user_name_create, uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id 
                FROM payment_accounts AS targetTable
                
                left join contractor c ON (c.id = targetTable.contractor_id)
                left join banks b ON (b.id = targetTable.bank_id)
                left join currencies cr ON (cr.id = targetTable.currency_id)
                left join entities e ON (e.id = c.ref_id and c.is_entity = 1)
                left join individuals i ON (i.id = c.ref_id and c.is_entity = 0)
                
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

        $refId = (int)Yii::$app->request->get('refId');
        $isEntity = Yii::$app->request->get('isEntity');

        $whereString = 'where targetTable.id > 0 ';
        if (!empty($refId) && $isEntity !== null){
            $whereString .= ' and c.ref_id ='.$refId.' AND c.is_entity='.$isEntity ;
        }

        $sql = 'SELECT targetTable.*, cr.currency_short_name as currency, b.bank_name, if(e.name is not null, e.name, i.full_name) as contractor_name, uc.user_name as user_name_create, uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id 
                FROM payment_accounts AS targetTable
                
                left join contractor c ON (c.id = targetTable.contractor_id)
                left join banks b ON (b.id = targetTable.bank_id)
                left join currencies cr ON (cr.id = targetTable.currency_id)
                left join entities e ON (e.id = c.ref_id and c.is_entity = 1)
                left join individuals i ON (i.id = c.ref_id and c.is_entity = 0)
                
                left join user uc ON (uc.user_id = targetTable.create_user)
                left join user uu ON (uu.user_id = targetTable.update_user)
                ';

        $sql .= $whereString;

        $items = Yii::$app->db->createCommand($sql)->queryAll();

        return json_encode(['items'=> $items]);
    }

    public function actionCreate()
    {

        $duplicate = false;
        $duplicate = PaymentAccountsRep::checkDuplicateByIBANOrAccount(Yii::$app->request->post('iban'), Yii::$app->request->post('account'));

        if ($duplicate) {
            return json_encode(['error' => 'Account with such credentials is already existed', 'duplicate' => true]);
        }

        try{
            $model = new PaymentAccounts();
            $model->contractor_id = Yii::$app->request->post('contractor_id');
            $model->bank_id = Yii::$app->request->post('bank_id');
            $model->account = Yii::$app->request->post('account');
            $model->iban = Yii::$app->request->post('iban');
            $model->currency_id = Yii::$app->request->post('currency_id');

            $model->create_user = Yii::$app->user->identity->id;
            $model->create_date = date('Y-m-d H:i:s', time());
            $model->save(false);

            return $model->id;
        } catch (\Exception $e){
            return json_encode(['error'=> 'Creating was no happened. Perhaps account with such credentials is already existed']);
        }
    }

    public function actionUpdate(int $id = null)
    {
        if ($id == null){
            $id = (int)Yii::$app->request->post('id');
        }

        $duplicate = false;
        $duplicate = PaymentAccountsRep::checkDuplicateByIBANOrAccount(Yii::$app->request->post('iban'), Yii::$app->request->post('account'), $id);

        if ($duplicate) {
            return json_encode(['error' => 'Account with such credentials is already existed', 'duplicate' => true]);
        }
        try{
            $model = PaymentAccounts::findOne($id);
            $model->contractor_id = Yii::$app->request->post('contractor_id');
            $model->bank_id = Yii::$app->request->post('bank_id');
            $model->account = Yii::$app->request->post('account');
            $model->iban = Yii::$app->request->post('iban');
            $model->currency_id = Yii::$app->request->post('currency_id');

            $model->update_user = Yii::$app->user->identity->id;
            $model->update_date = date('Y-m-d H:i:s', time());
            $model->save(false);
        } catch (\Exception $e){
            return json_encode(['error'=> 'Updating was no happened. Perhaps account with such credentials is already existed.']);
        }
    }

    public function actionDelete(int $id = null) : string
    {
        if ($id == null){
            $id = (int)Yii::$app->request->post('id');
        }

        $model = PaymentAccounts::findOne($id);
        if($model->delete()){
            return json_encode(['status' => true]);
        } else {
            return json_encode(['status' => false]);
        }

    }
}
