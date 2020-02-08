<?php

namespace app\controllers;

use app\models\Cities;
use app\models\Contractor;
use app\models\Countries;
use app\models\EntityTypes;
use app\models\Individuals;
use app\models\Regions;
use app\models\WorldParts;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class IndividualsController extends BaseController
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

        $sql = 'SELECT targetTable.*, uc.user_name as user_name_create, uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id 
                FROM individuals AS targetTable
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
        $sql = 'SELECT targetTable.*, uc.user_name as user_name_create, uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id 
                FROM individuals AS targetTable 
                left join user uc ON (uc.user_id = targetTable.create_user)
                left join user uu ON (uu.user_id = targetTable.update_user)
                ';

        $items = Yii::$app->db->createCommand($sql)->queryAll();

        return json_encode(['items'=> $items]);
    }

    public function actionGetAllForSelect()
    {
        $sql = 'SELECT i.id, i.full_name as name
                FROM individuals i
                ';

        $items = Yii::$app->db->createCommand($sql)->queryAll();

        return json_encode(['items'=> $items]);
    }

    public function actionCreate()
    {

        try{
            $model = new Individuals();
            $model->name = Yii::$app->request->post('name');
            $model->second_name = Yii::$app->request->post('second_name');
            $model->third_name = Yii::$app->request->post('third_name');
            $model->full_name = $model->third_name.' '.$model->name.' '.$model->second_name;
            $model->gender = Yii::$app->request->post('gender');
            $model->birthday = Yii::$app->request->post('birthday');
            $model->inn = Yii::$app->request->post('inn');
            $model->passport_series = Yii::$app->request->post('passport_series');
            $model->passport_number = Yii::$app->request->post('passport_number');
            $model->passport_authority = Yii::$app->request->post('passport_authority');
            $model->passport_authority_date = Yii::$app->request->post('passport_authority_date');
            $model->notice = Yii::$app->request->post('notice');

            $model->create_user = Yii::$app->user->identity->id;
            $model->create_date = date('Y-m-d H:i:s', time());
            $model->save(false);

            $contractor = new Contractor();
            $contractor->ref_id = $model->id;
            $contractor->is_entity = 0;
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

        $model = Individuals::findOne($id);
        $model->name = Yii::$app->request->post('name');
        $model->second_name = Yii::$app->request->post('second_name');
        $model->third_name = Yii::$app->request->post('third_name');
        $model->full_name = $model->third_name.' '.$model->name.' '.$model->second_name;
        $model->gender = Yii::$app->request->post('gender');
        $model->birthday = Yii::$app->request->post('birthday');
        $model->inn = Yii::$app->request->post('inn');
        $model->passport_series = Yii::$app->request->post('passport_series');
        $model->passport_number = Yii::$app->request->post('passport_number');
        $model->passport_authority = Yii::$app->request->post('passport_authority');
        $model->passport_authority_date = Yii::$app->request->post('passport_authority_date');
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

        $model = Individuals::findOne($id);
        if($model->delete()){
            $contractor = Contractor::findOne(['ref_id' => $id, 'is_entity' => 0]);
            if ($contractor != null){
                $contractor->delete();
            }
            return json_encode(['status' => true]);
        } else {
            return json_encode(['status' => false]);
        }

    }
}
