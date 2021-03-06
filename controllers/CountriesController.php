<?php

namespace app\controllers;

use app\models\Contractor;
use app\models\Countries;
use app\models\WorldParts;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class CountriesController extends BaseController
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
    public function actionGetById(int $id)
    {
        if ($id == null){
            $id = (int)Yii::$app->request->get('id');
        }

        $sql = 'SELECT targetTable.*, w.name as world_part, uc.user_name as user_name_create, uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id 
                FROM countries AS targetTable 
                left join world_parts w ON (w.id = targetTable.world_parts_id)
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
    public function actionGetByContractorId(int $contractorId = null)
    {
        if ($contractorId == null){
            $contractorId = (int)Yii::$app->request->get('contractorId');
        }

        $sql = 'SELECT targetTable.* 
                FROM contractor AS targetTable 
                where targetTable.id = :id
                ';

        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":id",$contractorId);

        /** @var Contractor $contractorEntity */
        $contractorEntity = $command->queryOne();

        if ($contractorEntity['is_entity'] == 1) {
            $table = 'entities';
        } else {
            return json_encode(['country_id' => 0]);
        }

        $sql = 'SELECT targetTable.country_id, c.id as currency_id
                FROM '.$table.' AS targetTable 
                left join currencies c ON (c.country_id = targetTable.country_id)
                where targetTable.id = :id
                ';

        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":id",$contractorEntity['ref_id']);
        $items = $command->queryOne();

        return json_encode($items);
    }

    /**
     * @return false|string
     * @throws \yii\db\Exception
     */
    public function actionGetAll()
    {
        $sql = 'SELECT targetTable.*,w.name as world_part, uc.user_name as user_name_create, uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id 
                FROM countries AS targetTable 
                left join world_parts w ON (w.id = targetTable.world_parts_id)
                left join user uc ON (uc.user_id = targetTable.create_user)
                left join user uu ON (uu.user_id = targetTable.update_user)
                ';

        $items = Yii::$app->db->createCommand($sql)->queryAll();

        return json_encode(['items'=> $items]);
    }

    public function actionCreate()
    {
        try{
            $wp = new Countries();
            $wp->name = Yii::$app->request->post('name');
            $wp->phone_code = Yii::$app->request->post('phone_code');
            $wp->phone_mask = Yii::$app->request->post('phone_mask');
            $wp->full_name = Yii::$app->request->post('full_name');
            $wp->alpha2 = Yii::$app->request->post('alpha2');
            $wp->alpha3 = Yii::$app->request->post('alpha3');
            $wp->iso = Yii::$app->request->post('iso');
            $wp->world_parts_id = Yii::$app->request->post('world_parts_id');
            $wp->flag_code = Yii::$app->request->post('flag_code');
            $wp->location = Yii::$app->request->post('location');
            $wp->iban_required = Yii::$app->request->post('iban_required');
            $wp->payment_account_required = Yii::$app->request->post('payment_account_required');

            $wp->create_user = Yii::$app->user->identity->id;
            $wp->create_date = date('Y-m-d H:i:s', time());
            $wp->save(false);

            return $wp->id;
        } catch (\Exception $e){
            return json_encode(['error'=> $e->getMessage()]);
        }
    }

    public function actionUpdate(int $id = null)
    {
        if ($id == null){
            $id = (int)Yii::$app->request->post('id');
        }

        $wp = Countries::findOne($id);
        $wp->name = Yii::$app->request->post('name');
        $wp->phone_code = Yii::$app->request->post('phone_code');
        $wp->phone_mask = Yii::$app->request->post('phone_mask');
        $wp->full_name = Yii::$app->request->post('full_name');
        $wp->alpha2 = Yii::$app->request->post('alpha2');
        $wp->alpha3 = Yii::$app->request->post('alpha3');
        $wp->iso = Yii::$app->request->post('iso');
        $wp->world_parts_id = Yii::$app->request->post('world_parts_id');
        $wp->flag_code = Yii::$app->request->post('flag_code');
        $wp->location = Yii::$app->request->post('location');
        $wp->iban_required = Yii::$app->request->post('iban_required');
        $wp->payment_account_required = Yii::$app->request->post('payment_account_required');

        $wp->update_user = Yii::$app->user->identity->id;
        $wp->update_date = date('Y-m-d H:i:s', time());
        $wp->save(false);
    }

    public function actionDelete(int $id = null) : string
    {
        if ($id == null){
            $id = (int)Yii::$app->request->post('id');
        }

        $inEntityTypes = $this->isPresentedIn('entity_types', 'country_id = '.$id);
        $inRegions= $this->isPresentedIn('regions', 'country_id = '.$id);
        $inProjectStages = $this->isPresentedIn('project_stages', 'country_id = '.$id);
        $inProjectParts = $this->isPresentedIn('project_parts', 'country_id = '.$id);
        if ($inEntityTypes || $inRegions || $inProjectStages || $inProjectParts) return json_encode(['status' => false, 'message' => '']);

        $wp = Countries::findOne($id);
        if($wp->delete()){
            return json_encode(['status' => true]);
        } else {
            return json_encode(['status' => false]);
        }

    }

    public function actionGetAllForSelect()
    {
        $sql = 'SELECT c.id, c.name 
                FROM countries c
                ';

        $items = Yii::$app->db->createCommand($sql)->queryAll();

        return json_encode(['items'=> $items]);
    }

    public function actionGetAllForSelectByProjectStages()
    {
        $sql = 'SELECT c.id, c.name 
                FROM countries c
                inner join project_stages ps ON (ps.country_id = c.id)
                group by c.id
                ';

        $items = Yii::$app->db->createCommand($sql)->queryAll();

        return json_encode(['items'=> $items]);
    }

    public function actionGetAllPhoneCodeList()
    {
        $sql = 'SELECT c.id, CONCAT("+", c.phone_code, " / ", c.name) as name, c.phone_code, c.phone_mask
                FROM countries c
                where c.phone_code is not null and c.phone_code != "" 
                group by c.id
                ';

        $items = Yii::$app->db->createCommand($sql)->queryAll();

        return json_encode(['items'=> $items]);
    }

    public function actionGetAllForSelectAccordingEntityTypes()
    {
        $sql = 'SELECT c.id, c.name 
                FROM countries c
                inner join entity_types et ON (et.country_id = c.id)
                group by c.id
                ';

        $items = Yii::$app->db->createCommand($sql)->queryAll();

        return json_encode(['items'=> $items]);
    }

    public function actionGetAllForSelectAccordingRegion()
    {
        $sql = 'SELECT c.name, c.id FROM regions
                inner join countries c on (c.id=regions.country_id)
                group by c.id
                ';

        $items = Yii::$app->db->createCommand($sql)->queryAll();

        return json_encode(['items'=> $items]);
    }

    public function actionGetForSelectAccordingRequests()
    {
        $sql = 'SELECT c.name, c.id FROM requests
                inner join countries c on (c.id=requests.country_id)
                group by c.id
                ';

        $items = Yii::$app->db->createCommand($sql)->queryAll();

        return json_encode(['items'=> $items]);
    }

    public function actionGetAllForSelectAccordingDocumentTypes()
    {
        Yii::$app->db->createCommand('SET sql_mode = ""')->execute();
        $sql = 'SELECT c.name, c.id, cur.id as currency_id FROM document_types
                inner join countries c on (c.id=document_types.country_id)
                inner join currencies cur ON (cur.country_id = c.id)
                group by c.id
                ';

        $items = Yii::$app->db->createCommand($sql)->queryAll();

        return json_encode(['items'=> $items]);
    }

    public function actionGetAllForSelectAccordingEntities()
    {
        $sql = 'SELECT c.name, c.id FROM countries c
                inner join entities e on (e.country_id=c.id)
                group by c.id
                ';

        $items = Yii::$app->db->createCommand($sql)->queryAll();

        return json_encode(['items'=> $items]);
    }

    public function actionGetAllForSelectAccordingBanks()
    {
        $sql = 'SELECT c.name, c.id FROM countries c
                inner join banks b on (b.country_id=c.id)
                group by c.id
                ';

        $items = Yii::$app->db->createCommand($sql)->queryAll();

        return json_encode(['items'=> $items]);
    }

    public function actionGetAllForSelectAccordingProjectParts()
    {
        $sql = 'SELECT c.name, c.id FROM project_parts
                inner join countries c on (c.id=project_parts.country_id)
                group by c.id
                ';

        $items = Yii::$app->db->createCommand($sql)->queryAll();

        return json_encode(['items'=> $items]);
    }
}
