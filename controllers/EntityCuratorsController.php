<?php

namespace app\controllers;

use app\models\EntityCurators;
use app\models\OwnCompanies;
use app\models\UserOwnCompany;
use Yii;
use yii\filters\VerbFilter;

class EntityCuratorsController extends BaseController
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
    public function actionGetAllByEntityId(int $entityId)
    {

        if ($entityId == null){
            $entityId = (int)Yii::$app->request->get('entityId');
        }

        $sql = 'SELECT targetTable.id as id, i.full_name as curator_full_name
                FROM entity_curators AS targetTable 
                left join entities AS e ON(targetTable.entity_id = e.id)
                left join individuals AS i ON(targetTable.curator_individual_id = i.id)
                where targetTable.entity_id = :entityId
                ';

        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":entityId",$entityId);
        $items = $command->queryAll();

        return json_encode(['items'=> $items]);
    }

    public function actionCreate()
    {
        try{
            $model = EntityCurators::findOne(['entity_id' => Yii::$app->request->post('entity_id'), 'curator_individual_id' =>Yii::$app->request->post('curator_individual_id')]);
            if (empty($model)) {
                $model = new EntityCurators();
            }

            $model->entity_id = Yii::$app->request->post('entity_id');
            $model->curator_individual_id = Yii::$app->request->post('curator_individual_id');
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

        $model = EntityCurators::findOne($id);

        if($model->delete()){
            return json_encode(['status' => true]);
        } else {
            return json_encode(['status' => false]);
        }
    }

}
