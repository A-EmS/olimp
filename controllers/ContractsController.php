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
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class ContractsController extends BaseController
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
                FROM banks AS targetTable 
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
        $sql = 'SELECT targetTable.*, c.name as country, uc.user_name as user_name_create, uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id 
                FROM banks AS targetTable 
                left join countries c ON (c.id = targetTable.country_id)
                left join user uc ON (uc.user_id = targetTable.create_user)
                left join user uu ON (uu.user_id = targetTable.update_user)
                ';

        $items = Yii::$app->db->createCommand($sql)->queryAll();

        return json_encode(['items'=> $items]);
    }

    public function actionCreate()
    {

        $duplicate = false;
        $duplicate = BanksRep::checkDuplicateByCountryAndCodeAndAccount(Yii::$app->request->post('country_id'), Yii::$app->request->post('bank_code'), Yii::$app->request->post('account'));

        if ($duplicate) {
            return json_encode(['error' => 'Bank with such code and account is already existed in selected country.', 'duplicate' => true]);
        }

        try{
            $model = new Banks();
            $model->country_id = Yii::$app->request->post('country_id');
            $model->bank_name = Yii::$app->request->post('bank_name');
            $model->bank_code = Yii::$app->request->post('bank_code');
            $model->account = Yii::$app->request->post('account');

            $model->create_user = Yii::$app->user->identity->id;
            $model->create_date = date('Y-m-d H:i:s', time());
            $model->save(false);

            return $model->id;
        } catch (\Exception $e){
            return json_encode(['error'=> 'Creating was no happened. Perhaps you have already have same bank in selected country']);
        }
    }

    public function actionUpdate(int $id = null)
    {
        if ($id == null){
            $id = (int)Yii::$app->request->post('id');
        }

        $duplicate = false;
        $duplicate = BanksRep::checkDuplicateByCountryAndCodeAndAccount(Yii::$app->request->post('country_id'), Yii::$app->request->post('bank_code'), Yii::$app->request->post('account'), $id);

        if ($duplicate) {
            return json_encode(['error' => 'Bank with such code and account is already existed in selected country.', 'duplicate' => true]);
        }

        try {
            $model = Banks::findOne($id);
            $model->country_id = Yii::$app->request->post('country_id');
            $model->bank_name = Yii::$app->request->post('bank_name');
            $model->bank_code = Yii::$app->request->post('bank_code');
            $model->account = Yii::$app->request->post('account');

            $model->update_user = Yii::$app->user->identity->id;
            $model->update_date = date('Y-m-d H:i:s', time());
            $model->save(false);
        } catch (\Exception $e){
            return json_encode(['error'=> 'Updating was no happened. Perhaps you have already have same bank in selected country.']);
        }
    }

    public function actionDelete(int $id = null) : string
    {
        if ($id == null){
            $id = (int)Yii::$app->request->post('id');
        }

        $model = Banks::findOne($id);

//        $deletable = !$this->isPresentedIn('cities', 'region_id = '.$id);
//        if (!$deletable) return json_encode(['status' => false, 'message' => '']);

        if($model->delete()){
            return json_encode(['status' => true]);
        } else {
            return json_encode(['status' => false]);
        }
    }

}
