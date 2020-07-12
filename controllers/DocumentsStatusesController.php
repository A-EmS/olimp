<?php

namespace app\controllers;

use app\models\DocumentsStatuses;
use app\models\WorldParts;
use app\repositories\DocumentsStatusesRep;
use app\repositories\ServicesRep;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class DocumentsStatusesController extends BaseController
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

        $sql = 'SELECT w.id, w.name, w.notice, w.create_date, w.update_date, uc.user_name as user_name_create, uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id 
                FROM documents_statuses w 
                left join user uc ON (uc.user_id = w.create_user)
                left join user uu ON (uu.user_id = w.update_user)
                where w.id = :id
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
        $sql = 'SELECT w.id, w.name, w.notice, w.create_date, w.update_date, uc.user_name as user_name_create, uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id 
                FROM documents_statuses w 
                left join user uc ON (uc.user_id = w.create_user)
                left join user uu ON (uu.user_id = w.update_user)
                ';

        $items = Yii::$app->db->createCommand($sql)->queryAll();

        return json_encode(['items'=> $items]);
    }

    public function actionCreate()
    {
        try{
            if (trim(Yii::$app->request->post('name')) != ''){
                if (DocumentsStatusesRep::checkDuplicateByName(Yii::$app->request->post('name'))){
                    return json_encode(['error' => 'Such status is already exist']);
                }
            }

            $wp = new DocumentsStatuses();
            $wp->name = Yii::$app->request->post('name');
            $wp->notice = Yii::$app->request->post('notice');
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

        if (trim(Yii::$app->request->post('name')) != ''){
            if (DocumentsStatusesRep::checkDuplicateByName(Yii::$app->request->post('name'), $id)){
                return json_encode(['error' => 'Such status is already exist']);
            }
        }

        $wp = DocumentsStatuses::findOne($id);
        $wp->name = Yii::$app->request->post('name');
        $wp->notice = Yii::$app->request->post('notice');
        $wp->update_user = Yii::$app->user->identity->id;
        $wp->update_date = date('Y-m-d H:i:s', time());
        $wp->save(false);
    }

    public function actionDelete(int $id = null) : string
    {
        if ($id == null){
            $id = (int)Yii::$app->request->post('id');
        }

        $wp = DocumentsStatuses::findOne($id);
        if($wp->delete()){
            return json_encode(['status' => true]);
        } else {
            return json_encode(['status' => false]);
        }

    }
}
