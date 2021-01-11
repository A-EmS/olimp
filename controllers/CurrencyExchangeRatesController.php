<?php

namespace app\controllers;

use app\models\CurrencyExchangeRates;
use app\models\Orders;
use app\models\ProjectData;
use app\models\Tills;
use app\repositories\CurrencyExchangeRatesRep;
use app\repositories\TillsRep;
use Yii;
use yii\filters\VerbFilter;

class CurrencyExchangeRatesController extends BaseController
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

        $sql = 'SELECT targetTable.*
                FROM currency_exchange_rates AS targetTable 
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
        $sql = 'SELECT targetTable.*,
                cr.currency_name as currency_base,
                cr_ref.currency_name as currency_ref,
                uc.user_name as user_name_create, uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id 
                FROM currency_exchange_rates AS targetTable 
                left join currencies cr ON (cr.id = targetTable.currency_id_base)
                left join currencies cr_ref ON (cr_ref.id = targetTable.currency_id_ref)
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

        $dateFrom = trim(Yii::$app->request->post('date_from'));
        $dateTo = trim(Yii::$app->request->post('date_to'));

        if (CurrencyExchangeRatesRep::checkDuplicateByCurrenciesAndDate(
            trim(Yii::$app->request->post('currency_id_base')),
            trim(Yii::$app->request->post('currency_id_ref')),
            $dateFrom
        )
        ){
            return json_encode(['error' => 'Such combination is already exist, please use filter to find all options with this currencies']);
        }

        if ($dateTo == null && CurrencyExchangeRatesRep::checkDuplicateByCurrenciesAndEmptyEndDate(
            trim(Yii::$app->request->post('currency_id_base')),
            trim(Yii::$app->request->post('currency_id_ref'))
        )){
            $item = CurrencyExchangeRates::findOne([
                'currency_id_base' => trim(Yii::$app->request->post('currency_id_base')),
                'currency_id_ref' => trim(Yii::$app->request->post('currency_id_ref')),
                'date_to' => null
            ]);
            if ((new \DateTime($item->date_from)) < (new \DateTime($dateFrom))) {
                $date = new \DateTime($dateFrom);
                $date->modify('-1 day');
                $item->date_to = $date->format('Y-m-d');
                $item->save(false);
            } else {
                return json_encode(['error' => 'Such combination with empty end date is already exist, please use filter to find all options with this currencies']);
            }
        }

        try{
            $model = new CurrencyExchangeRates();
            $model->currency_id_base = Yii::$app->request->post('currency_id_base');
            $model->currency_id_ref = Yii::$app->request->post('currency_id_ref');
            $model->rate_base = Yii::$app->request->post('rate_base');
            $model->rate_ref = Yii::$app->request->post('rate_ref');
            $model->date_from = $dateFrom;
            $model->date_to = Yii::$app->request->post('date_to', null);
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

//        if (CurrencyExchangeRatesRep::checkDuplicateByCurrenciesAndDate(
//            trim(Yii::$app->request->post('currency_id_base')),
//            trim(Yii::$app->request->post('currency_id_ref')),
//            trim(Yii::$app->request->post('date_from')),
//            $id
//        )
//        ){
//            return json_encode(['error' => 'Such combination is already exist']);
//        }

        $model = CurrencyExchangeRates::findOne($id);
        $model->currency_id_base = Yii::$app->request->post('currency_id_base');
        $model->currency_id_ref = Yii::$app->request->post('currency_id_ref');
        $model->rate_base = Yii::$app->request->post('rate_base');
        $model->rate_ref = Yii::$app->request->post('rate_ref');
        $model->date_from = Yii::$app->request->post('date_from');
        $model->date_to = Yii::$app->request->post('date_to', null);
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

        $model = CurrencyExchangeRates::findOne($id);

        if($model->delete()){
            return json_encode(['status' => true]);
        } else {
            return json_encode(['status' => false]);
        }

    }

    public function actionLoadTillForUser()
    {
        $sql = 'SELECT targetTable.*
                FROM tills AS targetTable 
                where targetTable.user_id = :user_id
                ';

        $userId = Yii::$app->user->identity->getId();
        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":user_id",$userId);
        $items = $command->queryOne();

        return json_encode((int)$items['user_id']);
    }
}
