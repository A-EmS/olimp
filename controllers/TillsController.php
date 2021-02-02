<?php

namespace app\controllers;

use app\models\Orders;
use app\models\ProjectData;
use app\models\Tills;
use app\repositories\TillsRep;
use Yii;
use yii\filters\VerbFilter;

class TillsController extends BaseController
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
                FROM tills AS targetTable 
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
        $sql = 'SELECT targetTable.*, u.user_name, cr.currency_name as currency, uc.user_name as user_name_create, uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id 
                FROM tills AS targetTable 
                left join currencies cr ON (cr.id = targetTable.currency_id)
                left join user u ON (u.user_id = targetTable.user_id)
                left join user uc ON (uc.user_id = targetTable.create_user)
                left join user uu ON (uu.user_id = targetTable.update_user)
                order by targetTable.id desc
                limit 1000
                ';

        $items = Yii::$app->db->createCommand($sql)->queryAll();

        return json_encode(['items'=> $items]);
    }

    public function actionGetAllExceptUserTill()
    {
        $sql = 'SELECT targetTable.*, CONCAT(targetTable.name, " ", "(", u.user_real, ")") as name, cr.currency_name as currency
                FROM tills AS targetTable 
                left join currencies cr ON (cr.id = targetTable.currency_id)
                left join user u ON (u.user_id = targetTable.user_id)
                where targetTable.user_id != :user_id
                order by targetTable.id desc
                limit 1000
                ';

        $userId = Yii::$app->user->identity->getId();
        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":user_id",$userId);

        $items = $command->queryAll();

        return json_encode(['items'=> $items]);
    }

    public function actionCreate()
    {

        if (trim(Yii::$app->request->post('name')) != '') {
            if (TillsRep::checkDuplicateByName(
                Yii::$app->request->post('name')
            )
            ){
                return json_encode(['error' => 'Such till is already exist']);
            }
        }

        try{
            $model = new Tills();
            $model->name = Yii::$app->request->post('name');
            $model->user_id = Yii::$app->request->post('user_id');
            $model->currency_id = Yii::$app->request->post('currency_id');
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

        if (trim(Yii::$app->request->post('name')) != ''){
            if (TillsRep::checkDuplicateByName(
                Yii::$app->request->post('name'),
                $id
            )
            ){
                return json_encode(['error' => 'Such till is already exist']);
            }
        }

        $model = Tills::findOne($id);
        $model->name = Yii::$app->request->post('name');
        $model->user_id = Yii::$app->request->post('user_id');
        $model->currency_id = Yii::$app->request->post('currency_id');
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

        $orderCount = Orders::find()->select('count(*)')
            ->where('till_id=:tillId')->params([':tillId' => $id])->count();
        $model = Tills::findOne($id);

        if ($orderCount > 0) {
            return json_encode(['error' => 'This till can not be removed, because it used']);
        }

        if($model->delete()){
            return json_encode(['status' => true]);
        } else {
            return json_encode(['status' => false]);
        }

    }

    public function actionLoadTillForUser()
    {
        $sql = 'SELECT targetTable.*
                FROM tills AS targetTable 
                where targetTable.user_id = :user_id
                ';

        $userId = Yii::$app->user->identity->getId();
        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":user_id",$userId);
        $items = $command->queryOne();

        return json_encode((int)$items['id']);
    }
}
