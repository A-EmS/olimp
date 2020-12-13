<?php

namespace app\controllers;

use app\models\OwnCompanies;
use app\models\UserOwnCompany;
use Yii;
use yii\filters\VerbFilter;

class UserOwnCompaniesController extends BaseController
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
    public function actionGetAll()
    {
        $sql = 'SELECT targetTable.*, c.name as country, CONCAT(if(e.entity_type_id is not null, et.short_name, ""), " ", e.short_name) as company, uc.user_name as user_name_create, uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id 
                FROM own_companies AS targetTable 
                left join entities e ON (e.id = targetTable.entity_id)
                left join entity_types et ON (et.id = e.entity_type_id)
                left join countries c ON (c.id = e.country_id)
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
    public function actionGetAllByUserId(int $userId)
    {

        if ($userId == null){
            $userId = (int)Yii::$app->request->get('userId');
        }

        $sql = 'SELECT targetTable.*, CONCAT(if(e.entity_type_id is not null, et.short_name, ""), " ", e.short_name) as company 
                FROM user_own_company AS targetTable 
                left join own_companies AS oc ON(targetTable.own_company_id = oc.id)
                left join entities e ON (e.id = oc.entity_id)
                left join entity_types et ON (et.id = e.entity_type_id)
                where targetTable.user_id = :user_id
                ';

        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":user_id",$userId);
        $items = $command->queryAll();

        return json_encode(['items'=> $items]);
    }

    public function actionCreate()
    {
        try{
            $model = UserOwnCompany::findOne(['user_id' => Yii::$app->request->post('user_id'), 'own_company_id' =>Yii::$app->request->post('own_company_id')]);
            if (empty($model)) {
                $model = new UserOwnCompany();
            }

            $model->user_id = Yii::$app->request->post('user_id');
            $model->own_company_id = Yii::$app->request->post('own_company_id');
            $model->save(false);

            return $model->id;
        } catch (\Exception $e){
            return json_encode(['error'=> $e->getMessage()]);
        }
    }

    public function actionDelete(int $id = null) : string
    {
        if ($id == null){
            $id = (int)Yii::$app->request->post('id');
        }

        $model = UserOwnCompany::findOne($id);

        if($model->delete()){
            return json_encode(['status' => true]);
        } else {
            return json_encode(['status' => false]);
        }
    }

}
