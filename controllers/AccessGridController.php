<?php

namespace app\controllers;

use app\models\AccessGrid;
use app\models\AcRole;
use app\models\Cities;
use app\models\Countries;
use app\models\EntityTypes;
use app\models\Regions;
use app\models\WorldParts;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class AccessGridController extends BaseController
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

    public function actionGetTable()
    {
        $sqlRows = 'SELECT targetTable.*
                FROM access_item AS targetTable
                order by name ASC
                ';
        $rows = Yii::$app->db->createCommand($sqlRows)->queryAll();

        $sqlColumns = 'SELECT targetTable.*
                FROM access_types AS targetTable
                ';
        $columns = Yii::$app->db->createCommand($sqlColumns)->queryAll();

        return json_encode(['tableItems'=> $rows, 'tableTypes' => $columns]);
    }

    public function actionGetByRoleId(int $id)
    {
        if ($id == null){
            $id = (int)Yii::$app->request->get('id');
        }

        $sql = 'SELECT targetTable.* 
                FROM access_grid AS targetTable 
                where targetTable.role_id = :id
                ';

        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":id",$id);
        $items = $command->queryAll();

        $preparedItems = [];
        foreach ($items as $item) {
            $preparedItems[$item['access_item_id']][$item['access_type_id']] = true;
        }

        return json_encode($preparedItems);
    }

    public function actionUpdate(int $id = null)
    {
        if ($id == null){
            $id = (int)Yii::$app->request->post('id');
        }

        AccessGrid::deleteAll(['role_id' => $id]);

        foreach (Yii::$app->request->post('checkedConfig') as $checkedConfigItem) {
            $model = new AccessGrid();
            $model->role_id = $id;
            $model->access_item_id = $checkedConfigItem['access_item_id'];
            $model->access_type_id = $checkedConfigItem['access_type_id'];
            $model->save(false);
        }

        return true;
    }
}
