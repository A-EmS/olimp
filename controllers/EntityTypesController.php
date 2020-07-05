<?php

namespace app\controllers;

use app\models\Countries;
use app\models\EntityTypes;
use app\models\WorldParts;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class EntityTypesController extends BaseController
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

        $sql = 'SELECT targetTable.*, c.name as country, uc.user_name as user_name_create, uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id 
                FROM entity_types AS targetTable 
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
                FROM entity_types AS targetTable 
                left join countries c ON (c.id = targetTable.country_id)
                left join user uc ON (uc.user_id = targetTable.create_user)
                left join user uu ON (uu.user_id = targetTable.update_user)
                ';

        $items = Yii::$app->db->createCommand($sql)->queryAll();

        return json_encode(['items'=> $items]);
    }

    /**
     * @return false|string
     * @throws \yii\db\Exception
     */
    public function actionGetAllForSelect()
    {
        $sql = 'SELECT et.id, et.short_name as name 
                FROM entity_types et
                ';

        $items = Yii::$app->db->createCommand($sql)->queryAll();

        return json_encode(['items'=> $items]);
    }

    /**
     * @param $countryId
     * @return false|string
     * @throws \yii\db\Exception
     */
    public function actionGetAllForSelectByCountry($countryId = null)
    {
        if ($countryId == null){
            $countryId = (int)Yii::$app->request->get('countryId');
        }

        $sql = 'SELECT et.id, et.short_name as name 
                FROM entity_types et
                where et.country_id = :countryId
                order by name ASC
                ';

        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":countryId",$countryId);
        $items = $command->queryAll();

        $newItem = new \stdClass();
        $newItem->id = null;
        $newItem->name = '<empty type>';
        array_unshift($items, $newItem);
        return json_encode(['items'=> $items]);
    }

    public function actionCreate()
    {

        try{
            $wp = new EntityTypes();
            $wp->name = Yii::$app->request->post('name');
            $wp->short_name = Yii::$app->request->post('short_name');
            $wp->notice = Yii::$app->request->post('notice');
            $wp->country_id = Yii::$app->request->post('country_id');

            $wp->create_user = Yii::$app->user->identity->id;
            $wp->create_date = date('Y-m-d H:i:s', time());
            $wp->save(false);

            return $wp->id;
        } catch (\Exception $e){
            return json_encode(['error'=> $e->getMessage()]);
        }
    }

    public function actionUpdate(int $id = null)
    {
        if ($id == null){
            $id = (int)Yii::$app->request->post('id');
        }

        $wp = EntityTypes::findOne($id);
        $wp->name = Yii::$app->request->post('name');
        $wp->short_name = Yii::$app->request->post('short_name');
        $wp->notice = Yii::$app->request->post('notice');
        $wp->country_id = Yii::$app->request->post('country_id');

        $wp->update_user = Yii::$app->user->identity->id;
        $wp->update_date = date('Y-m-d H:i:s', time());
        $wp->save(false);
    }

    public function actionDelete(int $id = null) : string
    {
        if ($id == null){
            $id = (int)Yii::$app->request->post('id');
        }

        $inEntities = $this->isPresentedIn('entities', 'entity_type_id = '.$id);
        if ($inEntities) return json_encode(['status' => false, 'message' => '']);

        $wp = EntityTypes::findOne($id);
        if($wp->delete()){
            return json_encode(['status' => true]);
        } else {
            return json_encode(['status' => false]);
        }

    }
}
