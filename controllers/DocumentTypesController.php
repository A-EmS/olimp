<?php

namespace app\controllers;

use app\models\DocumentTypes;
use app\repositories\DocumentTypesRep;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class DocumentTypesController extends BaseController
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

        $sql = 'SELECT targetTable.*
                FROM document_types targetTable 
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
                FROM document_types targetTable
                left join countries c ON (c.id=targetTable.country_id)
                left join user uc ON (uc.user_id = targetTable.create_user)
                left join user uu ON (uu.user_id = targetTable.update_user)
                order by targetTable.priority ASC
                ';

        $items = Yii::$app->db->createCommand($sql)->queryAll();

        return json_encode(['items'=> $items]);
    }

    public function actionGetScenarioTypes()
    {

        $items = [];
        foreach (DocumentTypesRep::SCENARIOS as $index => $text) {
            $items[]=['id' => $index, 'name' => $text];
        }
        return json_encode(['items'=> $items]);
    }

    /**
     * @return false|string
     * @throws \yii\db\Exception
     */
    public function actionGetByCountryId(int $countryId = null)
    {
        if ($countryId == null){
            $countryId = (int)Yii::$app->request->get('country_id');
        }

        $sql = 'SELECT targetTable.*
                FROM document_types targetTable
                left join countries c ON (c.id=targetTable.country_id)
                where targetTable.country_id = :country_id
                order by targetTable.priority ASC
                ';

        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":country_id",$countryId);
        $items = $command->queryAll();

        return json_encode(['items'=> $items]);
    }

    public function actionCreate()
    {
        try{
            if (trim(Yii::$app->request->post('name')) != ''){
                if (DocumentTypesRep::checkDuplicateByCountryAndName(Yii::$app->request->post('country_id'), Yii::$app->request->post('name'))){
                    return json_encode(['error' => 'Such type in this country is already exist']);
                }
            }

            $wp = new DocumentTypes();
            $wp->name = Yii::$app->request->post('name');
            $wp->country_id = Yii::$app->request->post('country_id');
            $wp->notice = Yii::$app->request->post('notice');
            $wp->priority = Yii::$app->request->post('priority');
            $wp->scenario_type = Yii::$app->request->post('scenario_type');
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
            if (DocumentTypesRep::checkDuplicateByCountryAndName(Yii::$app->request->post('country_id'), Yii::$app->request->post('name'), $id)){
                return json_encode(['error' => 'Such type in this country is already exist']);
            }
        }

        $wp = DocumentTypes::findOne($id);
        $wp->name = Yii::$app->request->post('name');
        $wp->country_id = Yii::$app->request->post('country_id');
        $wp->notice = Yii::$app->request->post('notice');
        $wp->priority = Yii::$app->request->post('priority');
        $wp->scenario_type = Yii::$app->request->post('scenario_type');
        $wp->update_user = Yii::$app->user->identity->id;
        $wp->update_date = date('Y-m-d H:i:s', time());
        $wp->save(false);
    }

    public function actionDelete(int $id = null) : string
    {
        if ($id == null){
            $id = (int)Yii::$app->request->post('id');
        }

        $wp = DocumentTypes::findOne($id);
        if($wp->delete()){
            return json_encode(['status' => true]);
        } else {
            return json_encode(['status' => false]);
        }

    }
}
