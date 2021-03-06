<?php

namespace app\controllers;


use app\models\DocumentTypes;
use app\models\Patterns;
use app\repositories\PatternsRep;
use Yii;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

class PatternsController extends BaseController
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
                FROM patterns AS targetTable 
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
        $sql = 'SELECT targetTable.id, targetTable.name, targetTable.own_company_id, targetTable.country_id, targetTable.filename, targetTable.document_type_id,  targetTable.create_date, 
       targetTable.update_date, 
       targetTable.code, targetTable.notice, uc.user_name as user_name_create, dt.name as document_type, ctr.name as country, ent.short_name as own_company, 
       uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id 
                FROM patterns AS targetTable 
                left join document_types as dt ON(dt.id = targetTable.document_type_id)
                left join countries ctr ON (ctr.id = targetTable.country_id)
                left join own_companies oc ON (oc.id = targetTable.own_company_id)
                left join entities ent ON (ent.id = oc.entity_id)
                left join user uc ON (uc.user_id = targetTable.create_user)
                left join user uu ON (uu.user_id = targetTable.update_user)
                ';

        $items = Yii::$app->db->createCommand($sql)->queryAll();

        return json_encode(['items'=> $items]);
    }


    public function actionGetAllForSelect()
    {

        $ownCompanyId = (int)Yii::$app->request->get('own_company_id');
        $countryId = (int)Yii::$app->request->get('country_id');
        $documentScenarioTypeId = (int)Yii::$app->request->get('document_type_id');

        $documentTypeModel = DocumentTypes::findOne(['country_id' => $countryId, 'scenario_type' => $documentScenarioTypeId ]);
        $documentTypeId = $documentTypeModel->id;

        $sql = 'SELECT targetTable.id, targetTable.name
                FROM patterns AS targetTable
                where 
                      targetTable.own_company_id = :own_company_id AND 
                      targetTable.country_id = :country_id AND 
                      targetTable.document_type_id = :document_type_id 
                ';

        $items = Yii::$app->db->createCommand($sql)
            ->bindParam(":own_company_id",$ownCompanyId)
            ->bindParam(":country_id",$countryId)
            ->bindParam(":document_type_id",$documentTypeId)
            ->queryAll();

        return json_encode(['items'=> $items]);
    }

    public function actionCreate()
    {

        if (trim(Yii::$app->request->post('name')) != ''){
            if (PatternsRep::checkDuplicate(Yii::$app->request->post('name'),Yii::$app->request->post('country_id'),Yii::$app->request->post('own_company_id'),Yii::$app->request->post('document_type_id'))){
                return json_encode(['error' => 'Such pattern is already exist']);
            }
        }

        try{
            $model = new Patterns();
            $model->name = Yii::$app->request->post('name');
            $model->code = Yii::$app->request->post('code');
            $model->country_id = Yii::$app->request->post('country_id');
            $model->document_type_id = Yii::$app->request->post('document_type_id');
            $model->own_company_id = Yii::$app->request->post('own_company_id');
            $model->notice = Yii::$app->request->post('notice');
            $model->filename = $_FILES['file']['name'];

            $model->create_user = Yii::$app->user->identity->id;
            $model->create_date = date('Y-m-d H:i:s', time());
            $model->save(false);

            $file = Patterns::getStorage() . $model->id .'_'. $model->filename ;
            move_uploaded_file($_FILES['file']['tmp_name'], $file);

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

        if (trim(Yii::$app->request->post('name')) != ''){
            if (PatternsRep::checkDuplicate(Yii::$app->request->post('name'),Yii::$app->request->post('country_id'),Yii::$app->request->post('own_company_id'),Yii::$app->request->post('document_type_id'), $id)){
                return json_encode(['error' => 'Such pattern is already exist']);
            }
        }

        $model = Patterns::findOne($id);
        $oldFileName = Patterns::getStorage() . $model->id .'_'. $model->filename;
        $model->name = Yii::$app->request->post('name');
        $model->code = Yii::$app->request->post('code');
        $model->country_id = Yii::$app->request->post('country_id');
        $model->document_type_id = Yii::$app->request->post('document_type_id');
        $model->own_company_id = Yii::$app->request->post('own_company_id');
        $model->notice = Yii::$app->request->post('notice');
        $model->filename = $_FILES['file']['name'];

        $model->update_user = Yii::$app->user->identity->id;
        $model->update_date = date('Y-m-d H:i:s', time());

        $file = Patterns::getStorage() . $model->id .'_'. $model->filename;

        if (file_exists($oldFileName)) {
            unlink($oldFileName);
        }
        move_uploaded_file($_FILES['file']['tmp_name'], $file);

        $model->save(false);
    }

    public function actionDelete(int $id = null) : string
    {
        if ($id == null){
            $id = (int)Yii::$app->request->post('id');
        }

        $model = Patterns ::findOne($id);

        $file = Patterns::getStorage() . $model->id .'_'. $model->filename ;
        if (file_exists($file)) {
            unlink($file);
        }

        if($model->delete()){
            return json_encode(['status' => true]);
        } else {
            return json_encode(['status' => false]);
        }

    }

    public function actionDownload(int $id = null)
    {
        if ($id == null){
            $id = (int)Yii::$app->request->post('id');
        }

        $model = Patterns ::findOne($id);
        return $model->getPatternFile();
    }
}
