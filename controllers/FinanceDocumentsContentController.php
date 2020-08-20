<?php

namespace app\controllers;

use app\models\Cities;
use app\models\Countries;
use app\models\EntityTypes;
use app\models\FinanceDocumentContent;
use app\models\FinanceDocuments;
use app\models\Regions;
use app\models\WorldParts;
use app\repositories\DocumentTypesRep;
use app\repositories\FinanceDocumentContentRep;
use app\repositories\FinanceDocumentsRep;
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
    public function actionGetServicesProductsList()
    {
        $documentId = (int)Yii::$app->request->get('documentId');

        $document = FinanceDocuments::findOne($documentId);
        $parentDocumentId = $document->parent_document_id;

        $sql = 'select p.name, f.id
                from finance_document_content f
                inner join products p ON (p.id = f.product_id)
                where f.document_id = :document_id
                order by f.product_id desc;
                ';

        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":document_id",$parentDocumentId);
        $products = $command->queryAll();


        $sql = 'select s.name, f.id
                from finance_document_content f
                inner join services s ON (s.id = f.service_id)
                where f.document_id = :document_id
                order by f.service_id desc
                ';

        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":document_id",$parentDocumentId);
        $services = $command->queryAll();

        return json_encode(['products'=> $products, 'services'=> $services]);
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

        if (FinanceDocumentContentRep::chekOnDuplicate(
            Yii::$app->request->post('document_id'),
            Yii::$app->request->post('product_id'),
            Yii::$app->request->post('service_id')
        )
        ){
            return json_encode(['error' => 'Such servise or product is already exist in current document']);
        }

        try{
            $financeDocument = FinanceDocuments::findOne(Yii::$app->request->post('document_id'));
            $contractId = $this->getContractByDocumentId(Yii::$app->request->post('document_id'));

            $model = new FinanceDocumentContent();
            $model->document_id = Yii::$app->request->post('document_id');
            $model->scenario_type = $financeDocument->scenario_type;
            $model->contract_id = $contractId;
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


            if (
                //CONTRACT / ANNEX
                $financeDocument->scenario_type == DocumentTypesRep::SCENARIO_TYPE_CONTRACT ||
                $financeDocument->scenario_type == DocumentTypesRep::SCENARIO_TYPE_ANNEX
            ) {
                if ($this->checkContractAnnexInvalidate()) {
                    return json_encode(['error' => 'One or more values less or equal 0']);
                }
            } elseif (
                //ACT
                $financeDocument->scenario_type == DocumentTypesRep::SCENARIO_TYPE_ACT
            ){
                if ($this->checkActInvalidate()) {
                    return json_encode(['error' => 'Required fields are not filled']);
                }

                $amounts = $this->checkAmountsForAct(0, $model->parent_content_id);

                if ($amounts['error'] == true || ($amounts['amount'] < $model->amount)) {
                    return json_encode(['error' => 'Amount is more then possible']);
                }

                $issetParentContent = FinanceDocumentContent::find()->where(
                    ['parent_content_id' => $model->parent_content_id, 'scenario_type' => DocumentTypesRep::SCENARIO_TYPE_ACT]
                )->one();

                if ($issetParentContent !== null) {
                    $issetParentContent->amount = $issetParentContent->amount + $model->amount;
                    $issetParentContent->save(false);
                    return $issetParentContent->id;
                }

                $parentRow = FinanceDocumentContent::findOne($model->parent_content_id);
                $model->product_id = $parentRow->product_id;
                $model->service_id = $parentRow->service_id;
            } elseif (
                //ACCOUNT
                $financeDocument->scenario_type == DocumentTypesRep::SCENARIO_TYPE_ACCOUNT
            ) {
                if ($this->checkAccountInvalidate()) {
                    return json_encode(['error' => 'Required fields are not filled']);
                }

                $issetParentContent = FinanceDocumentContent::find()->where(
                    ['parent_content_id' => $model->parent_content_id, 'scenario_type' => DocumentTypesRep::SCENARIO_TYPE_ACCOUNT]
                )->one();

                if ($issetParentContent !== null) {
                    $issetParentContent->amount = $issetParentContent->amount + $model->amount;
                    $issetParentContent->save(false);
                    return $issetParentContent->id;
                }

                $parentRow = FinanceDocumentContent::findOne($model->parent_content_id);
                $model->product_id = $parentRow->product_id;
                $model->service_id = $parentRow->service_id;
            } else {

            }


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

        if (FinanceDocumentContentRep::chekOnDuplicate(
            Yii::$app->request->post('document_id'),
            Yii::$app->request->post('product_id'),
            Yii::$app->request->post('service_id'),
            $id
            )
        ){
            return json_encode(['error' => 'Such servise or product is already exist in current document']);
        }

        $financeDocument = FinanceDocuments::findOne(Yii::$app->request->post('document_id'));
        $contractId = $this->getContractByDocumentId(Yii::$app->request->post('document_id'));

        $model = FinanceDocumentContent::findOne($id);
        $model->document_id = Yii::$app->request->post('document_id');
        $model->scenario_type = $financeDocument->scenario_type;
        $model->contract_id = $contractId;
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

        if (
            //CONTRACT / ANNEX
            $financeDocument->scenario_type == DocumentTypesRep::SCENARIO_TYPE_CONTRACT ||
            $financeDocument->scenario_type == DocumentTypesRep::SCENARIO_TYPE_ANNEX
        ) {
            if ($this->checkContractAnnexInvalidate()) {
                return json_encode(['error' => 'One or more values less or equal 0']);
            }
        } elseif (
            //ACT
            $financeDocument->scenario_type == DocumentTypesRep::SCENARIO_TYPE_ACT
        ){
            if ($this->checkActInvalidate()) {
                return json_encode(['error' => 'Required fields are not filled']);
            }

            $amounts = $this->checkAmountsForAct($id, $model->parent_content_id);

            if ($amounts['error'] == true || ($amounts['amount'] < $model->amount)) {
                return json_encode(['error' => 'Amount is more then possible']);
            }

            $parentRow = FinanceDocumentContent::findOne($model->parent_content_id);
            $model->product_id = $parentRow->product_id;
            $model->service_id = $parentRow->service_id;
        } elseif (
            //ACCOUNT
            $financeDocument->scenario_type == DocumentTypesRep::SCENARIO_TYPE_ACCOUNT
        ) {
            if ($this->checkAccountInvalidate()) {
                return json_encode(['error' => 'Required fields are not filled']);
            }

            $parentRow = FinanceDocumentContent::findOne($model->parent_content_id);
            $model->product_id = $parentRow->product_id;
            $model->service_id = $parentRow->service_id;
        } else {

        }

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

    public function actionCheckAmounts(int $rowId = null, int $parentContentId = null) : string
    {
        if ($rowId == null){
            $rowId = (int)Yii::$app->request->post('rowId', 0);
        }

        if ($parentContentId == null){
            $parentContentId = (int)Yii::$app->request->post('parentContentId');
        }

        $result = $this->checkAmountsForAct($rowId, $parentContentId);

        return json_encode($result);
    }

    public function actionCheckAccountItems(int $rowId = null, int $parentContentId = null) : string
    {
        if ($rowId == null){
            $rowId = (int)Yii::$app->request->post('rowId', 0);
        }

        if ($parentContentId == null){
            $parentContentId = (int)Yii::$app->request->post('parentContentId');
        }

        $result = $this->checkAmountsForAccount($rowId, $parentContentId);

        return json_encode($result);
    }

    protected function getContractByDocumentId($documentId) {
        $financeDocument = FinanceDocuments::findOne($documentId);

        if ($financeDocument->parent_document_id === null){
            $contractId = $financeDocument->id;
        } else {
            $financeParentDocument = FinanceDocuments::findOne($financeDocument->parent_document_id);
            if ($financeParentDocument->parent_document_id === null){
                $contractId = $financeParentDocument->id;
            } else {
                $financeContract = FinanceDocuments::findOne($financeParentDocument->parent_document_id);
                $contractId = $financeContract->id;
            }
        }

        return $contractId;
    }

    protected function checkContractAnnexInvalidate() {
        return (
                Yii::$app->request->post('amount') <= 0 ||
                Yii::$app->request->post('cost_without_tax') <= 0 ||
                Yii::$app->request->post('cost_with_tax') <= 0 ||
                Yii::$app->request->post('summ_without_tax') <= 0 ||
                Yii::$app->request->post('summ_with_tax') <= 0 ||
                Yii::$app->request->post('summ_tax') < 0
            );
    }

    protected function checkActInvalidate() {
        return (
                Yii::$app->request->post('amount') <= 0 ||
                (
                    Yii::$app->request->post('product_id') <= 0 &&
                    Yii::$app->request->post('service_id') <= 0
                )
            );
    }

    protected function checkAccountInvalidate() {
        return (
            Yii::$app->request->post('amount') <= 0 ||
            (
                Yii::$app->request->post('product_id') <= 0 &&
                Yii::$app->request->post('service_id') <= 0
            )
        );
    }

    protected function checkAmountsForAct($rowId, $parentContentId) {
        $resultArray = ['error' => false];

        $parentRow = FinanceDocumentContent::findOne($parentContentId);

        $sql = 'SELECT sum(amount) from finance_document_content where parent_content_id = :parent_content_id AND scenario_type = :scenario_type';

        $scenarioType = DocumentTypesRep::SCENARIO_TYPE_ACT;
        $command= Yii::$app->db->createCommand($sql);
        $command->bindParam(':parent_content_id', $parentContentId);
        $command->bindParam(':scenario_type', $scenarioType);
        $amount = $command->queryScalar();

        $availAmount = $parentRow['amount'] - $amount;

        $currentRow = FinanceDocumentContent::findOne($rowId);

        if ($currentRow !== false) {
            $availAmount = $availAmount + $currentRow['amount'];
        }

        if ($availAmount <= 0){
            $resultArray = ['error' => true, 'amount' => $availAmount];
        } else {
            $resultArray['amount'] = $availAmount;
        }

        return $resultArray;
    }

    protected function checkAmountsForAccount($rowId, $parentContentId) {
        $resultArray = ['error' => false];

        $parentRow = FinanceDocumentContent::findOne($parentContentId);

        $sql = 'SELECT sum(amount) from finance_document_content where parent_content_id = :parent_content_id AND scenario_type = :scenario_type';

        $scenarioType = DocumentTypesRep::SCENARIO_TYPE_ACCOUNT;
        $command= Yii::$app->db->createCommand($sql);
        $command->bindParam(':parent_content_id', $parentContentId);
        $command->bindParam(':scenario_type', $scenarioType);
        $amount = $command->queryScalar();

        $availAmount = $parentRow['amount'] - $amount;

        $currentRow = FinanceDocumentContent::findOne($rowId);

        if ($currentRow !== false) {
            $availAmount = $availAmount + $currentRow['amount'];
        }

        if ($availAmount <= 0){
            $resultArray = ['error' => true, 'amount' => $availAmount];
        } else {
            $resultArray['amount'] = $availAmount;
        }

        return $resultArray;
    }
}
