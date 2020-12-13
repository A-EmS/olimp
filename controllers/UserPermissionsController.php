<?php

namespace app\controllers;

use app\models\OwnCompanies;
use app\models\UserOwnCompany;
use app\repositories\UsersPermissionsRep;
use Yii;
use yii\filters\VerbFilter;

class UserPermissionsController extends BaseController
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
    public function actionGetAllByUserId(int $userId)
    {

        if ($userId == null){
            $userId = (int)Yii::$app->request->get('userId');
        }

        $sql = 'SELECT targetTable.* 
                FROM user_permissions AS targetTable 
                where targetTable.user_id = :user_id
                ';

        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":user_id",$userId);
        $items = $command->queryAll();

        $resultItems = [];
        foreach ($items as $item) {
            $resultItems[$item['key']] = $item;
        }

        $permissions = UsersPermissionsRep::USER_PERMISSIONS_LABELS;
        ksort($permissions);
        return json_encode(['items'=> $resultItems, 'permissions' => $permissions]);
    }

    public function actionChange()
    {
        $data = Yii::$app->request->post('data');

        if ((int)$data['id'] === 0) {
            $model = new UsersPermissionsRep();
            $model->key = $data['key'];
            $model->label = $data['label'];
            $model->user_id = $data['userId'];
            $model->status = $data['status'];
            $model->save(false);
        } else {
            $this->changeStatus($data['id'], $data['status']);
        }
    }

    protected function changeStatus(int $id, int $status) {

        $model = UsersPermissionsRep::findOne($id);
        $model->status = $status;
        $model->save(false);
    }

}
