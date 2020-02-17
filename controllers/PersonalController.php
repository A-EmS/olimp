<?php

namespace app\controllers;

use app\models\Contacts;
use app\models\ContactTypes;
use app\models\Countries;
use app\models\EntityTypes;
use app\models\Personal;
use app\models\WorldParts;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class PersonalController extends BaseController
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

        $sql = 'SELECT targetTable.*, e.name as entity_name, i.full_name as individual_name, uc.user_name as user_name_create, uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id 
                FROM personal AS targetTable
                
                left join entities e ON (e.id = targetTable.entity_id)
                left join individuals i ON (i.id = targetTable.individual_id)
                               
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
        $sql = 'SELECT targetTable.*, e.name as entity_name, i.full_name as individual_name, uc.user_name as user_name_create, uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id 
                FROM personal AS targetTable
                
                left join entities e ON (e.id = targetTable.entity_id)
                left join individuals i ON (i.id = targetTable.individual_id)
                
                left join user uc ON (uc.user_id = targetTable.create_user)
                left join user uu ON (uu.user_id = targetTable.update_user)
                ';

        $items = Yii::$app->db->createCommand($sql)->queryAll();

        return json_encode(['items'=> $items]);
    }

    /**
     * @param int|null $individualId
     * @return false|string
     * @throws \yii\db\Exception
     */
    public function actionGetForIndividual(int $individualId = null)
    {
        if ($individualId == null){
            $individualId = (int)Yii::$app->request->get('individual_id');
        }

        $sql = 'SELECT targetTable.id, targetTable.position, targetTable.notice, et.short_name as entity_type_name, e.id as entity_id, e.name as entity_name, e.short_name as entity_short_name 
                FROM personal AS targetTable
                
                left join entities e ON (e.id = targetTable.entity_id)
                left join entity_types et ON (et.id = e.entity_type_id)
                where targetTable.individual_id = :individual_id
                ';

        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":individual_id",$individualId);
        $items = $command->queryAll();

        return json_encode(['items'=> $items]);
    }

    /**
     * @param int|null $entityId
     * @return false|string
     * @throws \yii\db\Exception
     */
    public function actionGetForEntity(int $entityId = null)
    {
        if ($entityId == null){
            $entityId = (int)Yii::$app->request->get('entity_id');
        }

        $sql = 'SELECT targetTable.id, i.full_name, targetTable.position, targetTable.notice, i.id as individual_id
                FROM personal AS targetTable
                
                left join individuals i ON (i.id = targetTable.individual_id)
                where targetTable.entity_id = :entity_id
                ';

        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":entity_id",$entityId);
        $items = $command->queryAll();

        return json_encode(['items'=> $items]);
    }

    public function actionCreate()
    {

        try{
            $model = new Personal();
            $model->entity_id = Yii::$app->request->post('entity_id');
            $model->individual_id = Yii::$app->request->post('individual_id');
            $model->position = Yii::$app->request->post('position');
            $model->notice = Yii::$app->request->post('notice');

            $model->create_user = Yii::$app->user->identity->id;
            $model->create_date = date('Y-m-d H:i:s', time());
            $model->save(false);

            return $model->id;
        } catch (\Exception $e){
            return json_encode(['error'=> 'Data is not saved. Most often such personal line is already existed.']);
        }
    }

    public function actionUpdate(int $id = null)
    {
        if ($id == null){
            $id = (int)Yii::$app->request->post('id');
        }

        try{
            $model = Personal::findOne($id);
            $model->entity_id = Yii::$app->request->post('entity_id');
            $model->individual_id = Yii::$app->request->post('individual_id');
            $model->position = Yii::$app->request->post('position');
            $model->notice = Yii::$app->request->post('notice');

            $model->update_user = Yii::$app->user->identity->id;
            $model->update_date = date('Y-m-d H:i:s', time());
            $model->save(false);
        } catch (\Exception $e){
            return json_encode(['error'=> 'Data is not saved. Most often such personal line is already existed.']);
        }
    }

    public function actionDelete(int $id = null) : string
    {
        if ($id == null){
            $id = (int)Yii::$app->request->post('id');
        }

        $model = Personal::findOne($id);
        if($model->delete()){
            return json_encode(['status' => true]);
        } else {
            return json_encode(['status' => false]);
        }

    }
}
