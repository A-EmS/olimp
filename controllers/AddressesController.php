<?php

namespace app\controllers;

use app\models\Addresses;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class AddressesController extends BaseController
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

        $sql = 'SELECT targetTable.*, cities.name as city, if(e.name is not null, e.name, i.full_name) as contractor_name, `at`.address_type, uc.user_name as user_name_create, uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id 
                FROM addresses AS targetTable
                
                left join cities ON (cities.id = targetTable.city_id)
                left join address_types `at` ON (at.id = targetTable.address_type_id)
                left join contractor c ON (c.id = targetTable.contractor_id)
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

        $sql = 'SELECT targetTable.*, regions.name as region_name, countries.name as country_name, cities.name as city, if(e.name is not null, e.name, i.full_name) as contractor_name, `at`.address_type, uc.user_name as user_name_create, uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id 
                FROM addresses AS targetTable
                
                left join cities ON (cities.id = targetTable.city_id)
                left join regions ON (regions.id = cities.region_id)
                left join countries ON (countries.id = regions.country_id)
                left join address_types `at` ON (at.id = targetTable.address_type_id)
                left join contractor c ON (c.id = targetTable.contractor_id)
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

        try{
            $cityId = Yii::$app->request->post('city_id');

            $model = new Addresses();
            $model->contractor_id = Yii::$app->request->post('contractor_id');
            $model->address_type_id = Yii::$app->request->post('address_type_id');
            $model->country_id = Yii::$app->request->post('country_id');
            $model->region_id = Yii::$app->request->post('region_id');
            $model->city_id = ($cityId > 0) ? $cityId : 0;
            $model->index = Yii::$app->request->post('index');
            $model->address = Yii::$app->request->post('address');
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

        $cityId = Yii::$app->request->post('city_id');

        $model = Addresses::findOne($id);
        $model->contractor_id = Yii::$app->request->post('contractor_id');
        $model->address_type_id = Yii::$app->request->post('address_type_id');
        $model->country_id = Yii::$app->request->post('country_id');
        $model->region_id = Yii::$app->request->post('region_id');
        $model->city_id = ($cityId > 0) ? $cityId : 0;
        $model->index = Yii::$app->request->post('index');
        $model->address = Yii::$app->request->post('address');
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

        $model = Addresses::findOne($id);
        if($model->delete()){
            return json_encode(['status' => true]);
        } else {
            return json_encode(['status' => false]);
        }

    }
}
