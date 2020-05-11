<?php

namespace app\controllers;

use app\models\Contacts;
use app\models\ContactTypes;
use app\models\Countries;
use app\models\EntityTypes;
use app\models\WorldParts;
use app\repositories\ContactsRep;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class ContactsController extends BaseController
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

        $sql = 'SELECT targetTable.*, if(e.name is not null, e.name, i.full_name) as contractor_name, ctr.id as country_id, ctr.phone_code, ctr.phone_mask, ct.contact_type, uc.user_name as user_name_create, uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id 
                FROM contacts AS targetTable
                
                left join contact_types ct ON (ct.id = targetTable.contact_type_id)
                left join contractor c ON (c.id = targetTable.contractor_id)
                left join entities e ON (e.id = c.ref_id and c.is_entity = 1)
                left join individuals i ON (i.id = c.ref_id and c.is_entity = 0)
                left join countries ctr ON (ctr.id = targetTable.country_id)
                               
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

        $refId = (int)Yii::$app->request->get('refId');
        $isEntity = Yii::$app->request->get('isEntity');

        $whereString = 'where targetTable.id > 0 ';
        if (!empty($refId) && $isEntity !== null){
            $whereString .= ' and c.ref_id ='.$refId.' AND c.is_entity='.$isEntity ;
        }

        $sql = 'SELECT targetTable.*, if(e.name is not null, e.name, i.full_name) as contractor_name, ct.contact_type, uc.user_name as user_name_create, uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id 
                FROM contacts AS targetTable
                
                left join contact_types ct ON (ct.id = targetTable.contact_type_id)
                left join contractor c ON (c.id = targetTable.contractor_id)
                left join entities e ON (e.id = c.ref_id and c.is_entity = 1)
                left join individuals i ON (i.id = c.ref_id and c.is_entity = 0)
                
                left join user uc ON (uc.user_id = targetTable.create_user)
                left join user uu ON (uu.user_id = targetTable.update_user)
                ';

        $sql .= $whereString;


        $items = Yii::$app->db->createCommand($sql)->queryAll();

        return json_encode(['items'=> $items]);
    }

    public function actionCreate()
    {

        try{
            $forceAction = (Yii::$app->request->post('force_action') == 'true');

            $model = new Contacts();
            $model->contractor_id = Yii::$app->request->post('contractor_id');
            $model->contact_type_id = Yii::$app->request->post('contact_type_id');
            $model->country_id = Yii::$app->request->post('phoneCountryId');
            $model->name = trim(Yii::$app->request->post('name'));
            $model->notice = Yii::$app->request->post('notice');

            $duplicate = false;
            if (!$forceAction){
                $duplicate = ContactsRep::checkDuplicateByContactTypeAndName($model->contact_type_id, $model->name);

                if ($duplicate){
                    return json_encode(['error'=> 'Such contact is already existed. Do you want to create duplicate? Duplicate inside contractor will not be created.', 'duplicate' => true]);
                }
            }

            $model->create_user = Yii::$app->user->identity->id;
            $model->create_date = date('Y-m-d H:i:s', time());
            $model->save(false);

            return $model->id;
        } catch (\Exception $e){
            return json_encode(['error' => 'Contact is not saved. Most often you try to add same contact to contractor']);
        }
    }

    public function actionUpdate(int $id = null)
    {
        if ($id == null){
            $id = (int)Yii::$app->request->post('id');
        }

        try{
            $forceAction = (Yii::$app->request->post('force_action') == 'true');

            $model = Contacts::findOne($id);
            $model->contractor_id = Yii::$app->request->post('contractor_id');
            $model->contact_type_id = Yii::$app->request->post('contact_type_id');
            $model->country_id = Yii::$app->request->post('phoneCountryId');
            $model->name = trim(Yii::$app->request->post('name'));
            $model->notice = Yii::$app->request->post('notice');

            $duplicate = false;
            if (!$forceAction){
                $duplicate = ContactsRep::checkDuplicateByContactTypeAndName($model->contact_type_id, $model->name, $model->contractor_id);

                if ($duplicate){
                    return json_encode(['error'=> 'Such contact is already existed. Do you want to create duplicate? Duplicate inside contractor will not be updated.', 'duplicate' => true]);
                }
            }

            $model->update_user = Yii::$app->user->identity->id;
            $model->update_date = date('Y-m-d H:i:s', time());
            $model->save(false);
        }catch (\Exception $e){
            return json_encode(['error' => 'Contact is not saved. Most often you try to add same contact to contractor']);
        }
    }

    public function actionDelete(int $id = null) : string
    {
        if ($id == null){
            $id = (int)Yii::$app->request->post('id');
        }

        $model = Contacts::findOne($id);
        if($model->delete()){
            return json_encode(['status' => true]);
        } else {
            return json_encode(['status' => false]);
        }

    }

    public function actionFindByName($name = null)
    {
        if ($name == null){
            $name = (string)Yii::$app->request->get('name');
        }

        //mysql не 8, а нужна регулярка для выпила некоторых символов для сравнения
        $whereString = 'where          
                            lower(
                                replace(
                                     replace(
                                         replace(
                                            replace(targetTable.name, \'(\', \'\')
                                            ,\')\',
                                            \'\'),
                                         \' \',
                                         \'\'
                                     ),
                                     \'-\',
                                     \'\'
                                )
                             )
                            like "%'.strtolower(str_replace([' ', '(', ')', '-'], ['','','',''], $name)).'%"';

        $sql = 'SELECT targetTable.*, if(e.name is not null, e.name, i.full_name) as contractor_name, 
                    ct.contact_type
                FROM contacts AS targetTable
                
                left join contact_types ct ON (ct.id = targetTable.contact_type_id)
                left join contractor c ON (c.id = targetTable.contractor_id)
                left join entities e ON (e.id = c.ref_id and c.is_entity = 1)
                left join individuals i ON (i.id = c.ref_id and c.is_entity = 0)
                ';

        $sql .= $whereString;

        $sql .= ' limit 25 ';

        $items = Yii::$app->db->createCommand($sql)->queryAll();

        return json_encode(['items'=> $items]);

    }

    public function actionFindForHeaderSearch(int $id)
    {
        if ($id == null){
            $id = (int)Yii::$app->request->get('id');
        }

        $sql = 'SELECT targetTable.*, if(e.name is not null, e.name, i.full_name) as contractor_name, ctr.id as country_id, ctr.full_name as country_full_name, ctr.phone_code, ctr.phone_mask, ct.contact_type
                FROM contacts AS targetTable
                
                left join contact_types ct ON (ct.id = targetTable.contact_type_id)
                left join contractor c ON (c.id = targetTable.contractor_id)
                left join entities e ON (e.id = c.ref_id and c.is_entity = 1)
                left join individuals i ON (i.id = c.ref_id and c.is_entity = 0)
                left join countries ctr ON (ctr.id = targetTable.country_id)

                where targetTable.id = :id
                ';

        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":id",$id);
        $items = $command->queryOne();

        return json_encode(['items'=> $items]);
    }
}
