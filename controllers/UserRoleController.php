<?php

namespace app\controllers;

use app\models\AcUserRole;
use app\models\UserSettings;
use app\repositories\UsersRep;
use Yii;
use app\models\User;
use app\models\UserSearch;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

class UserRoleController extends BaseController
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
        ];
    }


    public function actionGetByUserId(int $id)
    {

        if ($id == null){
            $id = (int)Yii::$app->request->post('id');
        }

        $sql = 'SELECT acur_acr_id
                FROM ac_user_role
                where acur_user_id = :id
                ';

        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":id",$id);
        $items = $command->queryColumn();

        return json_encode(['items'=> $items]);
    }
}
