<?php

namespace app\controllers;

use app\models\AccessGrid;
use app\models\AccessTypes;
use app\models\AcRole;
use app\models\Cities;
use app\models\Countries;
use app\models\EntityTypes;
use app\models\Regions;
use app\models\WorldParts;
use app\repositories\ProjectsRep;
use app\repositories\RolesRep;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class RolesController extends BaseController
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
                FROM ac_role AS targetTable 
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
                FROM ac_role AS targetTable 
                left join user uc ON (uc.user_id = targetTable.create_user)
                left join user uu ON (uu.user_id = targetTable.update_user)
                ';

        $items = Yii::$app->db->createCommand($sql)->queryAll();

        return json_encode(['items'=> $items]);
    }


    public function actionGetAllForSelect()
    {
        $sql = 'SELECT c.id, c.name
                FROM ac_role c
                ';

        $items = Yii::$app->db->createCommand($sql)->queryAll();

        return json_encode(['items'=> $items]);
    }

    public function actionGetAllForUserForm()
    {
        $sql = 'SELECT c.id, c.name
                FROM ac_role c
                order by c.id ASC 
                ';

        $items = Yii::$app->db->createCommand($sql)->queryAll();

        return json_encode(['items'=> $items]);
    }

    public function actionCreate()
    {

        if (trim(Yii::$app->request->post('name')) != ''){
            if (RolesRep::checkDuplicateByName(Yii::$app->request->post('name'))){
                return json_encode(['error' => 'Such name of role is already exist']);
            }
        }

        try{
            $model = new AcRole();
            $model->name = Yii::$app->request->post('name');
            $model->description = Yii::$app->request->post('description');

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

        if (trim(Yii::$app->request->post('name')) != ''){
            if (RolesRep::checkDuplicateByName(Yii::$app->request->post('name'), $id)){
                return json_encode(['error' => 'Such name of role is already exist']);
            }
        }

        $model = AcRole::findOne($id);
        $model->name = Yii::$app->request->post('name');
        $model->description = Yii::$app->request->post('description');

        $model->update_user = Yii::$app->user->identity->id;
        $model->update_date = date('Y-m-d H:i:s', time());

        $model->save(false);
    }

    public function actionDelete(int $id = null) : string
    {
        if ($id == null){
            $id = (int)Yii::$app->request->post('id');
        }

        $model = AcRole ::findOne($id);
        if($model->delete()){
            AccessGrid::deleteAll(['role_id' => $id]);
            return json_encode(['status' => true]);
        } else {
            return json_encode(['status' => false]);
        }

    }

    public function actionActionAccess(string $action)
    {
        return true;
    }

    public function actionGetAccessList(string $accessLabelId = null)
    {
        if ($accessLabelId == null){
            $accessLabelId = (string)Yii::$app->request->post('accessLabelId');
        }

        $resultList = [];

        if (Yii::$app->user->identity->isAdmin) {
            $sqlAdmin = 'SELECT name
                        FROM access_types
                        ';

            $command = Yii::$app->db->createCommand($sqlAdmin);
            $items = $command->queryColumn();
            foreach ($items as $item) {
                $resultList[$item] = true;
            }

            return json_encode(['items' => $resultList]);
        }

        $userId = (int)Yii::$app->user->identity->getId();

        $sqlRoles = 'SELECT id
        FROM access_item
        where name = :name
        ';

        $command = Yii::$app->db->createCommand($sqlRoles);
        $command->bindParam(":name",$accessLabelId);
        $accessItemId = $command->queryScalar();



        $sqlRoles = 'SELECT acur_acr_id
        FROM ac_user_role
        where acur_user_id = :userId
        ';

        $command = Yii::$app->db->createCommand($sqlRoles);
        $command->bindParam(":userId",$userId);
        $roleIds = $command->queryColumn();


        $sqlRoles = '
            SELECT atp.name
            FROM access_grid AS targetTable
            left join access_types atp ON (atp.id = targetTable.access_type_id)
            where targetTable.access_item_id = :accessItemId AND targetTable.role_id IN ('.implode(',', $roleIds).')
            group by atp.name
        ';


        $command = Yii::$app->db->createCommand($sqlRoles);
        $command->bindParam(":accessItemId",$accessItemId);
        $items = $command->queryColumn();

        foreach ($items as $item) {
            $resultList[$item] = true;
        }

        return json_encode(['items' => $resultList]);
    }

}
