<?php

namespace app\controllers;

use app\models\Cities;
use app\models\Contractor;
use app\models\Countries;
use app\models\Entities;
use app\models\EntityTypes;
use app\models\Individuals;
use app\models\Regions;
use app\models\WorldParts;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class EntitiesController extends BaseController
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

        $sql = 'SELECT targetTable.*, et.id as entity_type_id, et.short_name as entity_type_short_name, uc.user_name as user_name_create, uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id 
                FROM entities AS targetTable
                left join entity_types et ON (et.id = targetTable.entity_type_id)
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
        $sql = 'SELECT targetTable.*, et.id as entity_type_id, et.short_name as entity_type_name, uc.user_name as user_name_create, uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id 
                FROM entities AS targetTable
                left join entity_types et ON (et.id = targetTable.entity_type_id)
                left join user uc ON (uc.user_id = targetTable.create_user)
                left join user uu ON (uu.user_id = targetTable.update_user)
                ';

        $items = Yii::$app->db->createCommand($sql)->queryAll();

        return json_encode(['items'=> $items]);
    }

    public function actionCreate()
    {

        try{
            $model = new Entities();
            $model->entity_type_id = Yii::$app->request->post('entity_type_id');
            $model->name = $this->addBrackets(Yii::$app->request->post('name'));
            $model->short_name = $this->addBrackets(Yii::$app->request->post('short_name'));
            $model->ogrn = Yii::$app->request->post('ogrn');
            $model->inn = Yii::$app->request->post('inn');
            $model->kpp = Yii::$app->request->post('kpp');
            $model->okpo = Yii::$app->request->post('okpo');
            $model->notice = Yii::$app->request->post('notice');

            $model->create_user = Yii::$app->user->identity->id;
            $model->create_date = date('Y-m-d H:i:s', time());
            $model->save(false);

            $contractor = new Contractor();
            $contractor->ref_id = $model->id;
            $contractor->is_entity = 1;
            $contractor->save( false);

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

        $model = Entities::findOne($id);
        $model->entity_type_id = Yii::$app->request->post('entity_type_id');
        $model->name = $this->addBrackets(Yii::$app->request->post('name'));
        $model->short_name = $this->addBrackets(Yii::$app->request->post('short_name'));
        $model->ogrn = Yii::$app->request->post('ogrn');
        $model->inn = Yii::$app->request->post('inn');
        $model->kpp = Yii::$app->request->post('kpp');
        $model->okpo = Yii::$app->request->post('okpo');
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

        $model = Entities::findOne($id);
        if($model->delete()){
            $contractor = Contractor::findOne(['ref_id' => $id, 'is_entity' => 1]);
            if ($contractor != null){
                $contractor->delete();
            }
            return json_encode(['status' => true]);
        } else {
            return json_encode(['status' => false]);
        }

    }

    protected function addBrackets($val){

        $lq = html_entity_decode('&laquo;');
        $rq = html_entity_decode('&raquo;');

        $arrayOfString = mb_str_split($val);

        if ($arrayOfString[0] !== $lq){
            $val = $lq.$val;
        }

        if ($arrayOfString[count($arrayOfString)-1] !== $rq){
            $val = $val.$rq;
        }

//        $val = preg_replace("/$lq/", '', $val);
//        $val = preg_replace("/$rq/", '', $val);

        return $val;
    }
}
