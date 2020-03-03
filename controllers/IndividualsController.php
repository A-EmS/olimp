<?php

namespace app\controllers;

use app\models\Addresses;
use app\models\Cities;
use app\models\Contacts;
use app\models\Contractor;
use app\models\Countries;
use app\models\EntityTypes;
use app\models\Individuals;
use app\models\Personal;
use app\models\Regions;
use app\models\WorldParts;
use app\repositories\ContactsRep;
use app\repositories\IndividualsRep;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class IndividualsController extends BaseController
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
                FROM individuals AS targetTable
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
        $sql = 'SELECT targetTable.*, uc.user_name as user_name_create, uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id 
                FROM individuals AS targetTable 
                left join user uc ON (uc.user_id = targetTable.create_user)
                left join user uu ON (uu.user_id = targetTable.update_user)
                ';

        $items = Yii::$app->db->createCommand($sql)->queryAll();

        return json_encode(['items'=> $items]);
    }

    public function actionGetAllForSelect()
    {
        $sql = 'SELECT i.id, i.full_name as name, CONCAT(i.full_name, " (", i.id, ")") as nameWithId
                FROM individuals i
                ';

        $items = Yii::$app->db->createCommand($sql)->queryAll();

        return json_encode(['items'=> $items]);
    }

    public function actionCreate()
    {

        if (trim(Yii::$app->request->post('inn')) != ''){
            if (IndividualsRep::existByINN(Yii::$app->request->post('inn'))){
                return json_encode(['error' => 'Such inn is already exist']);
            }
        }
        if (trim(Yii::$app->request->post('passport_series')) !== '' && trim(Yii::$app->request->post('passport_number')) !== '') {
            if (IndividualsRep::existByPassport(Yii::$app->request->post('passport_number'), Yii::$app->request->post('passport_series'))){
                return json_encode(['error' => 'Such passport data is already exist']);
            }
        }

        $forceAction = (Yii::$app->request->post('force_action') == 'true');

        if (!$forceAction && is_array(Yii::$app->request->post('pullContacts')) && count(Yii::$app->request->post('pullContacts')) > 0) {
            foreach (Yii::$app->request->post('pullContacts') as $contactItem){
                $duplicate = false;
                $duplicate = ContactsRep::checkDuplicateByContactTypeAndName($contactItem['contact_type_id'], $contactItem['contact_name']);

                if ($duplicate) {
                    return json_encode(['error' => 'Contractor with such contacts is already existed. Do you want to create duplicate?', 'duplicate' => true]);
                }
            }
        }

        try{
            $model = new Individuals();
            $model->name = Yii::$app->request->post('name');
            $model->second_name = Yii::$app->request->post('second_name');
            $model->third_name = Yii::$app->request->post('third_name');
            $model->full_name = $model->third_name.' '.$model->name.' '.$model->second_name;
            $model->gender = Yii::$app->request->post('gender');
            $model->birthday = Yii::$app->request->post('birthday');
            $model->inn = Yii::$app->request->post('inn');
            $model->passport_series = Yii::$app->request->post('passport_series');
            $model->passport_number = Yii::$app->request->post('passport_number');
            $model->passport_authority = Yii::$app->request->post('passport_authority');
            $model->passport_authority_date = Yii::$app->request->post('passport_authority_date');
            $model->notice = Yii::$app->request->post('notice');

            $model->create_user = Yii::$app->user->identity->id;
            $model->create_date = date('Y-m-d H:i:s', time());
            $model->save(false);

            $contractor = new Contractor();
            $contractor->ref_id = $model->id;
            $contractor->is_entity = 0;
            $contractor->save( false);

            if (is_array(Yii::$app->request->post('pullContacts')) && count(Yii::$app->request->post('pullContacts')) > 0){
                foreach (Yii::$app->request->post('pullContacts') as $contactItem){
                    $contact = new Contacts();
                    $contact->name = $contactItem['contact_name'];
                    $contact->contact_type_id = $contactItem['contact_type_id'];
                    $contact->notice = $contactItem['contact_notice'];
                    $contact->country_id = $contactItem['phoneCountryId'];
                    $contact->contractor_id = $contractor->id;

                    $contact->create_user = Yii::$app->user->identity->id;
                    $contact->create_date = date('Y-m-d H:i:s', time());
                    $contact->save(false);
                }
            }

            if (is_array(Yii::$app->request->post('pullEntities')) && count(Yii::$app->request->post('pullEntities')) > 0){
                foreach (Yii::$app->request->post('pullEntities') as $personalItem){
                    $personal = new Personal();
                    $personal->entity_id = $personalItem['entity_id'];
                    $personal->individual_id = $model->id;
                    $personal->position = $personalItem['position'];
                    $personal->notice = $personalItem['notice'];

                    $personal->create_user = Yii::$app->user->identity->id;
                    $personal->create_date = date('Y-m-d H:i:s', time());
                    $personal->save(false);
                }
            }

            if (is_array(Yii::$app->request->post('pullAddresses')) && count(Yii::$app->request->post('pullAddresses')) > 0){
                foreach (Yii::$app->request->post('pullAddresses') as $addressItem){
                    $address = new Addresses();
                    $address->contractor_id = $contractor->id;
                    $address->address_type_id = $addressItem['address_type_id'];
                    $address->region_id = $addressItem['region_id_for_contacts'];
                    $address->country_id = $addressItem['country_id_for_contacts'];
                    $address->city_id = $addressItem['city_id'];
                    $address->index = $addressItem['index'];
                    $address->address = $addressItem['address'];
                    $address->notice = $addressItem['notice'];

                    $address->create_user = Yii::$app->user->identity->id;
                    $address->create_date = date('Y-m-d H:i:s', time());
                    $address->save(false);
                }
            }

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

        if (trim(Yii::$app->request->post('inn')) != ''){
            if (IndividualsRep::existByINN(Yii::$app->request->post('inn'), $id)){
                return json_encode(['error' => 'Such inn is already exist']);
            }
        }
        if (trim(Yii::$app->request->post('passport_series')) !== '' && trim(Yii::$app->request->post('passport_number')) !== '') {
            if (IndividualsRep::existByPassport(Yii::$app->request->post('passport_number'), Yii::$app->request->post('passport_series'), $id)){
                return json_encode(['error' => 'Such passport data is already exist']);
            }
        }

        $model = Individuals::findOne($id);
        $model->name = Yii::$app->request->post('name');
        $model->second_name = Yii::$app->request->post('second_name');
        $model->third_name = Yii::$app->request->post('third_name');
        $model->full_name = $model->third_name.' '.$model->name.' '.$model->second_name;
        $model->gender = Yii::$app->request->post('gender');
        $model->birthday = Yii::$app->request->post('birthday');
        $model->inn = Yii::$app->request->post('inn');
        $model->passport_series = Yii::$app->request->post('passport_series');
        $model->passport_number = Yii::$app->request->post('passport_number');
        $model->passport_authority = Yii::$app->request->post('passport_authority');
        $model->passport_authority_date = Yii::$app->request->post('passport_authority_date');
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

        $model = Individuals::findOne($id);
        if($model->delete()){
            $contractor = Contractor::findOne(['ref_id' => $id, 'is_entity' => 0]);
            if ($contractor != null){

                Contacts::deleteAll(['contractor_id' => $contractor->id]);

                Personal::deleteAll(['individual_id' => $id]);

                Addresses::deleteAll(['contractor_id' => $contractor->id]);

                $contractor->delete();
            }
            return json_encode(['status' => true]);
        } else {
            return json_encode(['status' => false]);
        }

    }
}
