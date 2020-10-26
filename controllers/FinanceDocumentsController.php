<?php

namespace app\controllers;

use app\models\DocumentTypes;
use app\models\FinanceDocumentContent;
use app\models\FinanceDocuments;
use app\repositories\DocumentTypesRep;
use app\repositories\FinanceDocumentsRep;
use Yii;
use yii\filters\VerbFilter;

class FinanceDocumentsController extends BaseController
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

    public function actionGetAllForInvoiceByContractor($contractorId)
    {

        $sql = 'SELECT targetTable.id, targetTable.document_code as name
                FROM finance_documents as targetTable
                where (targetTable.scenario_type = :scenario_type_contract || targetTable.scenario_type = :scenario_type_annex) and targetTable.contractor_id = :contractor_id
                order by targetTable.scenario_type ASC, targetTable.document_code ASC
                ';

        $contractScenario = DocumentTypesRep::SCENARIO_TYPE_CONTRACT;
        $annexScenario = DocumentTypesRep::SCENARIO_TYPE_ANNEX;

        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":scenario_type_contract",$contractScenario);
        $command->bindParam(":scenario_type_annex",$annexScenario);
        $command->bindParam(":contractor_id",$contractorId);
        $items = $command->queryAll();

        return json_encode(['items'=> $items]);
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
        $sql = 'SELECT targetTable.*, dt.scenario_type, manager.id as individual_id_manager, manager.full_name as individual_id_manager_full_name,
                contractor_manager.id as contractor_individual_id_manager, contractor_manager.full_name as contractor_individual_id_manager_full_name
                FROM finance_documents as targetTable
                left join document_types as dt ON(dt.id = targetTable.document_type_id)
                left join individuals as manager ON(manager.id = targetTable.individual_id_manager)
                left join contractor as c ON(c.id = targetTable.contractor_id)
                left join individuals as contractor_manager ON(contractor_manager.id = c.individual_id_manager)
                where targetTable.id = :id
                ';

        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":id",$id);
        $items = $command->queryOne();

        return json_encode($items);
    }

    public function actionGetRowById($id)
    {
        if ($id == null){
            $id = (int)Yii::$app->request->get('id');
        }
        $sql = 'SELECT targetTable.*
                FROM finance_documents as targetTable
                where targetTable.id = :id
                ';

        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":id",$id);
        $items = $command->queryOne();

        return json_encode($items);
    }

    public function actionGetAllByContractor($contractorId, $id, $currentDocumentTypeScenario, $countryId)
    {
        if ($contractorId == null){
            $contractorId = Yii::$app->request->get('contractorId');
        }

        if ($id == null){
            $id = Yii::$app->request->get('id');
        }

        if ($currentDocumentTypeScenario == null){
            $currentDocumentTypeScenario = Yii::$app->request->get('currentDocumentTypeScenario');
        }

        if (empty($countryId)){
            $countryId = Yii::$app->request->get('countryId', null);
        }

        $documentParentTypeScenarios = [];
        if ($currentDocumentTypeScenario == DocumentTypesRep::SCENARIO_TYPE_CONTRACT) {
            $documentParentTypeScenarios = [0];
        } else if ($currentDocumentTypeScenario == DocumentTypesRep::SCENARIO_TYPE_ANNEX) {
            $documentParentTypeScenarios = [DocumentTypesRep::SCENARIO_TYPE_CONTRACT];
        } else if ($currentDocumentTypeScenario == DocumentTypesRep::SCENARIO_TYPE_AD_AGREEMENT) {
            $documentParentTypeScenarios = [DocumentTypesRep::SCENARIO_TYPE_CONTRACT];
        } else if ($currentDocumentTypeScenario == DocumentTypesRep::SCENARIO_TYPE_ACCOUNT) {
            $documentParentTypeScenarios = [DocumentTypesRep::SCENARIO_TYPE_CONTRACT, DocumentTypesRep::SCENARIO_TYPE_ANNEX];
        } else if ($currentDocumentTypeScenario == DocumentTypesRep::SCENARIO_TYPE_ACT) {
            $documentParentTypeScenarios = [DocumentTypesRep::SCENARIO_TYPE_CONTRACT, DocumentTypesRep::SCENARIO_TYPE_ANNEX];
        }

        $inString = '';
        foreach ($documentParentTypeScenarios as $documentParentTypeScenario) {
            $inString .= '"'.$documentParentTypeScenario.'",';
        }

        $inString = trim($inString, ',');

        $sql = 'SELECT targetTable.id
                FROM document_types as targetTable
                where targetTable.scenario_type IN ('.$inString.') AND country_id = :countryId
                order by targetTable.id desc
                ';

        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":countryId",$countryId);
        $docTypesToQuery = $command->queryAll();

        $inQueryString = '';
        foreach ($docTypesToQuery as $docType) {
            $inQueryString .= '"'.$docType['id'].'",';
        }

        $inQueryString = trim($inQueryString, ',');
        if($inQueryString === '') {
            $inQueryString = 0;
        }
        $sql = 'SELECT targetTable.id, CONCAT(dt.name, " ", targetTable.date,  " №:", targetTable.document_code) as document_code
                FROM finance_documents as targetTable
                left join document_types dt ON (dt.id = targetTable.document_type_id)
                where targetTable.contractor_id=:contractorId AND targetTable.id != :id AND targetTable.document_type_id IN ('.$inQueryString.')
                order by targetTable.id desc
                ';

        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":id",$id);
        $command->bindParam(":contractorId",$contractorId);
        $items = $command->queryAll();

        return json_encode(['items'=> $items]);
    }

    public function actionGetAllByPage($page = null, $perPage = null, $filters = null)
    {
        $page = (int)Yii::$app->request->post('page', 0);
        $perPage = (int)Yii::$app->request->post('perPage', 50);
        $filters = Yii::$app->request->post('filters', []);

        $whereString = ' targetTable.id > 0 ';
        if (!empty($filters)) {
            if (isset($filters['id']) && !empty($filters['id'])) {
                $whereString .= ' AND targetTable.id = '.$filters['id'].' ';
            }


            if (isset($filters['document_code']) && !empty($filters['document_code'])) {
                $whereString .= ' AND targetTable.document_code LIKE \'%'.$filters['document_code'].'%\' ';
            }
            if (isset($filters['parent_document']) && !empty($filters['parent_document'])) {
                $whereString .= ' AND fd.document_code LIKE \'%'.$filters['parent_document'].'%\' ';
            }
            if (isset($filters['contractor']) && !empty($filters['contractor'])) {
                $whereString .= ' AND (e.short_name LIKE \'%'.$filters['contractor'].'%\' || i.full_name LIKE \'%'.$filters['contractor'].'%\')';
            }
            if (isset($filters['country_id']) && !empty($filters['country_id'])) {
                $whereString .= ' AND targetTable.country_id='.$filters['country_id'].' ';
            }
            if (isset($filters['document_status_id']) && !empty($filters['document_status_id'])) {
                $whereString .= ' AND targetTable.document_status_id='.$filters['document_status_id'].' ';
            }
            if (isset($filters['document_type_id']) && !empty($filters['document_type_id'])) {
                $whereString .= ' AND targetTable.document_type_id='.$filters['document_type_id'].' ';
            }
            if (isset($filters['own_company_id']) && !empty($filters['own_company_id'])) {
                $whereString .= ' AND targetTable.own_company_id='.$filters['own_company_id'].' ';
            }
            if (isset($filters['contractor_id']) && !empty($filters['contractor_id'])) {
                $whereString .= ' AND targetTable.contractor_id='.$filters['contractor_id'].' ';
            }
            if (isset($filters['date']) && !empty($filters['date'])) {
                $whereString .= ' AND targetTable.date = \''.$filters['date'].'\' ';
            }
            if (isset($filters['notice']) && !empty($filters['notice'])) {
                $whereString .= ' AND targetTable.notice LIKE \'%'.$filters['notice'].'%\' ';
            }
            if (isset($filters['user_name_create']) && !empty($filters['user_name_create'])) {
                $whereString .= ' AND uc.user_name LIKE \'%'.$filters['user_name_create'].'%\' ';
            }
            if (isset($filters['create_date']) && !empty($filters['create_date'])) {
                $whereString .= ' AND targetTable.create_date LIKE \'%'.trim($filters['create_date']).'%\' ';
            }
            if (isset($filters['user_name_update']) && !empty($filters['user_name_update'])) {
                $whereString .= ' AND uu.user_name_update LIKE \'%'.$filters['user_name_update'].'%\' ';
            }
            if (isset($filters['update_date']) && !empty($filters['update_date'])) {
                $whereString .= ' AND targetTable.update_date LIKE \'%'.trim($filters['update_date']).'%\' ';
            }
        }


        $sql = 'SELECT targetTable.id, targetTable.document_code, fd.document_code as parent_document, targetTable.date, 
                 if(e.short_name is not null, CONCAT(if(et.short_name is not null, et.short_name, ""), " ", e.short_name), i.full_name) as contractor,
                 cnt.name as country, dt.name as document_type,
                targetTable.notice, ds.name as document_status, ent.short_name as own_company,
                targetTable.create_date, targetTable.update_date,
                uc.user_name as user_name_create, uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id 
                FROM finance_documents as targetTable 
                                
                left join finance_documents fd ON (fd.id = targetTable.parent_document_id)
                
                left join contractor c ON (c.id = targetTable.contractor_id)
                left join entities e ON (e.id = c.ref_id and c.is_entity = 1)
                left join entity_types et ON (et.id = e.entity_type_id)
                left join individuals i ON (i.id = c.ref_id and c.is_entity = 0)
                
                left join countries cnt ON (cnt.id = targetTable.country_id)
                left join document_types dt ON (dt.id = targetTable.document_type_id)
                left join own_companies oc ON (oc.id = targetTable.own_company_id)
                left join entities ent ON (ent.id = oc.entity_id)
                left join documents_statuses ds ON (ds.id = targetTable.document_status_id)
                left join currencies cr ON (cr.id = targetTable.currency_id)
                
                
                left join user uc ON (uc.user_id = targetTable.create_user)
                left join user uu ON (uu.user_id = targetTable.update_user)
                where '.$whereString.'
                order by targetTable.id desc
                limit :limit
                offset :offset
                ';

        $offset = ($page * $perPage) - $perPage;
        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":limit",$perPage);
        $command->bindParam(":offset", $offset);
        $items = $command->queryAll();

        $sqlCount = 'SELECT count(*)
                FROM finance_documents as targetTable 
                                
                left join finance_documents fd ON (fd.id = targetTable.parent_document_id)
                
                left join contractor c ON (c.id = targetTable.contractor_id)
                left join entities e ON (e.id = c.ref_id and c.is_entity = 1)
                left join entity_types et ON (et.id = e.entity_type_id)
                left join individuals i ON (i.id = c.ref_id and c.is_entity = 0)
                
                left join countries cnt ON (cnt.id = targetTable.country_id)
                left join document_types dt ON (dt.id = targetTable.document_type_id)
                left join own_companies oc ON (oc.id = targetTable.own_company_id)
                left join entities ent ON (ent.id = oc.entity_id)
                left join documents_statuses ds ON (ds.id = targetTable.document_status_id)
                left join currencies cr ON (cr.id = targetTable.currency_id)
                
                left join user uc ON (uc.user_id = targetTable.create_user)
                left join user uu ON (uu.user_id = targetTable.update_user)
                where '.$whereString.'
                ';
        $commandCount = Yii::$app->db->createCommand($sqlCount);
        $count = $commandCount->queryScalar();

        return json_encode(['items'=> $items, 'count' => $count]);
    }

    public function actionCreate()
    {
        try{

            if (FinanceDocumentsRep::existByParams(
                Yii::$app->request->post('own_company_id'),
                Yii::$app->request->post('document_type_id'),
                Yii::$app->request->post('date'),
                Yii::$app->request->post('document_code')
            )
            ){
                return json_encode(['error' => 'Such combination Own Company + Document Type + Date + Document Code is already exist']);
            }

            /** @var DocumentTypes $documentType */
            $documentType = DocumentTypes::findOne(Yii::$app->request->post('document_type_id'));

            $wp = new FinanceDocuments();
            $wp->document_code = Yii::$app->request->post('document_code');
            $wp->percent = Yii::$app->request->post('percent');
            $wp->parent_document_id = Yii::$app->request->post('parent_document_id');
            $wp->date = Yii::$app->request->post('date');
            $wp->contractor_id = Yii::$app->request->post('contractor_id');
            $wp->country_id = Yii::$app->request->post('country_id');
            $wp->currency_id = Yii::$app->request->post('currency_id');
            $wp->document_type_id = Yii::$app->request->post('document_type_id');
            $wp->scenario_type = $documentType->scenario_type;
            $wp->own_company_id = Yii::$app->request->post('own_company_id');
            $wp->document_status_id = Yii::$app->request->post('document_status_id');
            $wp->currency_id = Yii::$app->request->post('currency_id');
            $wp->notice = Yii::$app->request->post('notice');
            $wp->individual_id_manager = ($wp->scenario_type == DocumentTypesRep::SCENARIO_TYPE_CONTRACT) ? Yii::$app->request->post('individual_id_manager') : 0;

            $wp->create_user = Yii::$app->user->identity->id;
            $wp->create_date = date('Y-m-d H:i:s', time());
            $wp->save(false);



            return json_encode(['id'=> $wp->id, 'scenario_type' => $documentType->scenario_type]);
        } catch (\Exception $e){
            return 0;
        }
    }

    public function actionUpdate(int $id = null)
    {
        if ($id == null){
            $id = (int)Yii::$app->request->post('id');
        }

        if (FinanceDocumentsRep::existByParams(
            Yii::$app->request->post('own_company_id'),
            Yii::$app->request->post('document_type_id'),
            Yii::$app->request->post('date'),
            Yii::$app->request->post('document_code'),
            $id
        )
        ){
            return json_encode(['error' => 'Such combination Own Company + Document Type + Date + Document Code is already exist']);
        }

        /** @var DocumentTypes $documentType */
        $documentType = DocumentTypes::findOne(Yii::$app->request->post('document_type_id'));

        $wp = FinanceDocuments::findOne($id);

        if ($wp->scenario_type == DocumentTypesRep::SCENARIO_TYPE_ANNEX && $wp->parent_document_id != Yii::$app->request->post('parent_document_id')) {
            $this->updateAnnexContentContract($wp, Yii::$app->request->post('parent_document_id'));
        }

        $wp->document_code = Yii::$app->request->post('document_code');
        $wp->percent = Yii::$app->request->post('percent');
        $wp->parent_document_id = Yii::$app->request->post('parent_document_id');
        $wp->date = Yii::$app->request->post('date');
        $wp->contractor_id = Yii::$app->request->post('contractor_id');
        $wp->country_id = Yii::$app->request->post('country_id');
        $wp->currency_id = Yii::$app->request->post('currency_id');
        $wp->document_type_id = Yii::$app->request->post('document_type_id');
        $wp->scenario_type = $documentType->scenario_type;
        $wp->own_company_id = Yii::$app->request->post('own_company_id');
        $wp->document_status_id = Yii::$app->request->post('document_status_id');
        $wp->currency_id = Yii::$app->request->post('currency_id');
        $wp->notice = Yii::$app->request->post('notice');
        $wp->individual_id_manager = ($wp->scenario_type == DocumentTypesRep::SCENARIO_TYPE_CONTRACT) ? Yii::$app->request->post('individual_id_manager') : 0;

        $wp->update_user = Yii::$app->user->identity->id;
        $wp->update_date = date('Y-m-d H:i:s', time());
        $wp->save(false);
    }

    public function actionDelete(int $id = null) : string
    {
        if ($id == null){
            $id = (int)Yii::$app->request->post('id');
        }

        $childCount = FinanceDocuments::find()->select('count(*)')
            ->where('parent_document_id=:parent_document_id')->params([':parent_document_id' => $id])->count();

        $hasContent = FinanceDocumentContent::find()->select('count(*)')
            ->where('document_id=:document_id')->params([':document_id' => $id])->count();

        if ($childCount > 0 || $hasContent > 0) {
            return json_encode(['error' => 'This document can not be removed, because it contains child document or content']);
        }

        $wp = FinanceDocuments::findOne($id);
        if($wp->delete()){
            return json_encode(['status' => true]);
        } else {
            return json_encode(['status' => false]);
        }
    }

    protected function updateAnnexContentContract(FinanceDocuments $model, $contractId) {
        $internalDocuments = FinanceDocuments::findAll(['parent_document_id' => $model->id]);
        foreach ($internalDocuments as $internalDocument) {
            FinanceDocumentContent::updateAll(['contract_id' => $contractId], ['document_id' => $internalDocument['id']]);
        }

    }
}
