<?php

namespace app\controllers;

use app\models\Cities;
use app\models\Countries;
use app\models\EntityTypes;
use app\models\FinanceDocumentContent;
use app\models\FinanceDocuments;
use app\models\Regions;
use app\models\WorldParts;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class FinanceDocumentsContentController extends BaseController
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

        $sql = 'SELECT targetTable.*
                FROM finance_document_content AS targetTable
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
//        $sql = 'SELECT targetTable.*, r.name as region, c.name as country, uc.user_name as user_name_create, uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id
//                FROM cities AS targetTable
//                left join regions r ON (r.id = targetTable.region_id)
//                left join countries c ON (c.id = r.country_id)
//                left join user uc ON (uc.user_id = targetTable.create_user)
//                left join user uu ON (uu.user_id = targetTable.update_user)
//                ';
//
//        $items = Yii::$app->db->createCommand($sql)->queryAll();
        $items = [0 => 'Document content 1'];
        return json_encode(['items'=> $items]);
    }

    /**
     * @return false|string
     * @throws \yii\db\Exception
     */
    public function actionGetAllByDocumentId()
    {
        $documentId = (int)Yii::$app->request->get('documentId');

        $sql = 'SELECT targetTable.*, s.name as service, p.name as product, fd.document_code as document, 
                uc.user_name as user_name_create, uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id
                FROM finance_document_content AS targetTable
                left join finance_documents fd ON (fd.id = targetTable.document_id)
                left join services s ON (s.id = targetTable.service_id)
                left join products p ON (p.id = targetTable.product_id)
                left join user uc ON (uc.user_id = targetTable.create_user)
                left join user uu ON (uu.user_id = targetTable.update_user)
                where targetTable.document_id = :document_id
                ';

        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":document_id",$documentId);
        $items = $command->queryAll();

        return json_encode(['items'=> $items]);
    }

    public function actionCreate()
    {

        try{
            $model = new FinanceDocumentContent();
            $model->document_id = Yii::$app->request->post('document_id');
            $model->parent_content_id = Yii::$app->request->post('parent_content_id');
            $model->percent = Yii::$app->request->post('percent');
            $model->product_id = Yii::$app->request->post('product_id');
            $model->service_id = Yii::$app->request->post('service_id');
            $model->amount = Yii::$app->request->post('amount');
            $model->cost_without_tax = Yii::$app->request->post('cost_without_tax');
            $model->cost_with_tax = Yii::$app->request->post('cost_with_tax');
            $model->summ_without_tax = Yii::$app->request->post('summ_without_tax');
            $model->summ_with_tax = Yii::$app->request->post('summ_with_tax');
            $model->summ_tax = Yii::$app->request->post('summ_tax');
            $model->notice = Yii::$app->request->post('notice');

            $model->create_user = Yii::$app->user->identity->id;
            $model->create_date = date('Y-m-d H:i:s', time());
            $model->save(false);

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

        $model = FinanceDocumentContent::findOne($id);
        $model->document_id = Yii::$app->request->post('document_id');
        $model->parent_content_id = Yii::$app->request->post('parent_content_id');
        $model->percent = Yii::$app->request->post('percent');
        $model->product_id = Yii::$app->request->post('product_id');
        $model->service_id = Yii::$app->request->post('service_id');
        $model->amount = Yii::$app->request->post('amount');
        $model->cost_without_tax = Yii::$app->request->post('cost_without_tax');
        $model->cost_with_tax = Yii::$app->request->post('cost_with_tax');
        $model->summ_without_tax = Yii::$app->request->post('summ_without_tax');
        $model->summ_with_tax = Yii::$app->request->post('summ_with_tax');
        $model->summ_tax = Yii::$app->request->post('summ_tax');
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

        $childCount = FinanceDocumentContent::find()->select('count(*)')
            ->where('parent_content_id=:parent_content_id')->params([':parent_content_id' => $id])->count();

        if ($childCount > 0) {
            return json_encode(['error' => 'This row can not be removed, because it used like parent']);
        }

        $model = FinanceDocumentContent::findOne($id);
        if($model->delete()){
            return json_encode(['status' => true]);
        } else {
            return json_encode(['status' => false]);
        }
    }
}
