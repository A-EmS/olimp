<?php

namespace app\controllers;

use app\models\Contacts;
use app\models\ContactTypes;
use app\models\Countries;
use app\models\EntityTypes;
use app\models\WorldParts;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class ContractorsController extends BaseController
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
        //no used yet
        $items = [];
        return json_encode(['items'=> $items]);
        $sql = 'SELECT targetTable.*, if(e.name is not null, e.name, i.name) as name 
                FROM contractor AS targetTable
                left join entities e ON (e.id = targetTable.ref_id and targetTable.is_entity = 1)
                left join individuals i ON (i.id = targetTable.ref_id and targetTable.is_entity = 0)
                ';

        $items = Yii::$app->db->createCommand($sql)->queryAll();

        return json_encode(['items'=> $items]);
    }

    public function actionGetAllForSelect()
    {
        $sql = 'SELECT targetTable.*, if(e.name is not null, e.name, i.full_name) as name 
                FROM contractor AS targetTable
                left join entities e ON (e.id = targetTable.ref_id and targetTable.is_entity = 1)
                left join individuals i ON (i.id = targetTable.ref_id and targetTable.is_entity = 0)
                ';

        $items = Yii::$app->db->createCommand($sql)->queryAll();

        return json_encode(['items'=> $items]);
    }

    public function actionGetAllForProjectSelect()
    {
        $sql = 'SELECT targetTable.*, 
                    if(e.short_name is not null, CONCAT(if(et.short_name is not null, et.short_name, ""), " ", e.short_name), i.full_name) as name 
                FROM contractor AS targetTable
                left join entities e ON (e.id = targetTable.ref_id and targetTable.is_entity = 1)
                left join entity_types et ON (et.id = e.entity_type_id)
                left join individuals i ON (i.id = targetTable.ref_id and targetTable.is_entity = 0)
                ';

        $items = Yii::$app->db->createCommand($sql)->queryAll();

        return json_encode(['items'=> $items]);
    }

    public function actionGetByRefIdAndType()
    {
        $refId = (int)Yii::$app->request->get('refId');
        $isEntity = (int)Yii::$app->request->get('isEntity');

        $sql = 'SELECT targetTable.*, if(e.name is not null, e.name, i.full_name) as name, manager.id as individual_id_manager, manager.full_name as individual_id_manager_full_name
                FROM contractor AS targetTable
                left join entities e ON (e.id = targetTable.ref_id and targetTable.is_entity = 1)
                left join individuals i ON (i.id = targetTable.ref_id and targetTable.is_entity = 0)
                left join individuals manager ON (manager.id = targetTable.individual_id_manager)
                where targetTable.ref_id ='.$refId.' and targetTable.is_entity = '.$isEntity.'
                ';

        $item = Yii::$app->db->createCommand($sql)->queryOne();

        return json_encode(['item'=> $item]);
    }

    public function actionGetManagerByContractorId(int $contractorId = null)
    {
        $contractorId = (int)Yii::$app->request->get('contractorId');

        $sql = 'SELECT manager.id as individual_id_manager, manager.full_name as individual_id_manager_full_name
                FROM contractor AS targetTable
                left join individuals manager ON (manager.id = targetTable.individual_id_manager)
                where targetTable.id ='.$contractorId.'
                ';

        $item = Yii::$app->db->createCommand($sql)->queryOne();

        return json_encode(['item'=> $item]);
    }

}
