<?php

namespace app\controllers;

use app\models\AccessGrid;
use app\models\AcRole;
use app\models\AcUserRole;
use app\models\UserSettings;
use app\repositories\RolesRep;
use app\repositories\UsersRep;
use Yii;
use app\models\User;
use app\models\UserSearch;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends BaseController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
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

            /*
            'access' => [
                'class' => \yii\filters\AccessControl::class,
                'rules' => [
                    [
                        'allow' => false,
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        //'roles' => ['admin'],
                    ],
                ],
            ],
            */

        ];
    }

    public function actionIndex()
    {
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    public function actionGetById(int $id)
    {
        if ($id == null){
            $id = (int)Yii::$app->request->get('id');
        }

        $sql = 'SELECT targetTable.* 
                FROM user AS targetTable
                where targetTable.user_id = :id
                ';

        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":id",$id);
        $items = $command->queryOne();

        return json_encode($items);
    }

    public function actionGetAll()
    {
        $sql = 'SELECT targetTable.*
                FROM user AS targetTable 
                ';

        $items = Yii::$app->db->createCommand($sql)->queryAll();

        return json_encode(['items'=> $items]);
    }

    public function actionCreate()
    {

        if (trim(Yii::$app->request->post('user_name')) != ''){
            if (UsersRep::checkDuplicateByLogin(
                Yii::$app->request->post('user_name')
            )
            ){
                return json_encode(['error' => 'Such login is already exist']);
            }
        }

        try{
            $model = new User();
            $model->user_name = Yii::$app->request->post('user_name');
            $model->user_pwd = Yii::$app->request->post('user_pwd', '');
            $model->user_real = Yii::$app->request->post('user_real');
            $model->notice = Yii::$app->request->post('notice');

//            $model->create_user = Yii::$app->user->identity->id;
//            $model->create_date = date('Y-m-d H:i:s', time());
            $model->save(false);

            foreach (Yii::$app->request->post('userRoleConfig', []) as $userRoleId) {
                $modelAcUserRole = new AcUserRole();
                $modelAcUserRole->acur_user_id = $model->user_id;
                $modelAcUserRole->acur_acr_id = $userRoleId;
                $modelAcUserRole->acur_create_user = Yii::$app->user->identity->username;
                $modelAcUserRole->acur_create_time = date('Y-m-d H:i:s', time());
                $modelAcUserRole->save(false);
            }

            return $model->user_id;
        } catch (\Exception $e){
            return json_encode(['error'=> $e->getMessage()]);
        }
    }

    public function actionUpdate(int $id = null)
    {
        if ($id == null){
            $id = (int)Yii::$app->request->post('id');
        }

        $model = User::findOne($id);
        $pwd = !empty(Yii::$app->request->post('user_pwd')) ? password_hash(Yii::$app->request->post('user_pwd'), PASSWORD_BCRYPT, [12]) : $model->user_pwd;

        if (trim(Yii::$app->request->post('user_name')) != ''){
            if (UsersRep::checkDuplicateByLogin(
                    Yii::$app->request->post('user_name'),
                    $id
                )
            ){
                return json_encode(['error' => 'Such login is already exist']);
            }
        }

        $model->user_name = Yii::$app->request->post('user_name');
        $model->user_pwd = Yii::$app->request->post('user_pwd', '');
        $model->user_real = Yii::$app->request->post('user_real');
        $model->notice = Yii::$app->request->post('notice');

//        $upd = User::findOne(Yii::$app->user->identity->id);
//        $model->user_update_user = $upd->user_name;
//        $model->user_update_time = date('Y-m-d H:i:s', time());

        $model->save(false);

        AcUserRole::deleteAll(['acur_user_id' => $id]);

        foreach (Yii::$app->request->post('userRoleConfig', []) as $userRoleId) {
            $modelAcUserRole = new AcUserRole();
            $modelAcUserRole->acur_user_id = $id;
            $modelAcUserRole->acur_acr_id = $userRoleId;
            $modelAcUserRole->acur_create_user = Yii::$app->user->identity->username;
            $modelAcUserRole->acur_create_time = date('Y-m-d H:i:s', time());
            $modelAcUserRole->save(false);
        }
    }

    public function actionDelete(int $id = null) : string
    {
        if ($id == null){
            $id = (int)Yii::$app->request->post('id');
        }

        $model = User ::findOne($id);
        if($model->delete()){
            UserSettings::deleteAll(['user_id' => $id]);
//            AcUserRole::deleteAll(['acrf_acr_id' => $id]);
            return json_encode(['status' => true]);
        } else {
            return json_encode(['status' => false]);
        }

    }

    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }

    public function actionSetUserSetting($userId = null, $settingKey = null, $settingValue = null){

        if ($userId == null){
            $userId = (int)Yii::$app->request->post('user_id');
        }

        if ($settingKey == null){
            $settingKey = (string)Yii::$app->request->post('key');
        }

        if ($settingValue == null){
            $settingValue = (string)Yii::$app->request->post('value');
        }

        $setting = UserSettings::findOne(['user_id'=>$userId, 'key'=>$settingKey]);

        if (empty($setting)){
            $setting = new UserSettings();
        }

        $setting->user_id = $userId;
        $setting->key = $settingKey;
        $setting->value = $settingValue;
        $setting->save(false);

    }

    public function actionSetUserSettings($userId = null, $settingString = null){
        if ($userId == null){
            $userId = (int)Yii::$app->request->post('user_id');
        }
        if ($settingString == null){
            $settingStringArray = json_decode((string)Yii::$app->request->post('setting_string'));
        }

        foreach ($settingStringArray as $settingKey => $settingValue) {
            $this->actionSetUserSetting($userId, $settingKey, $settingValue);
        }
    }
}
