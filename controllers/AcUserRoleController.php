<?php

namespace app\controllers;

use app\models\AcFunc;
use app\models\AcRole;
use Yii;
use app\models\AcUserRole;
use app\models\User;
use app\models\AcRoleSearch;
use yii\web\Controller;
use yii\web\HttpException;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * AcUserRoleController implements the CRUD actions for AcUserRole model.
 */
class AcUserRoleController extends Controller
{
    private static $masterPage = 'user/index';

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
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
                    ],
                ],
            ],        
        ];
    }

    /**
     * Lists all AcUserRole models.
     * @return mixed
     */
    public function actionIndex($user_id)
    {
        if (!$user = User::find()->leftJoin(AcUserRole::tableName(), 'user_id = acur_user_id')->leftJoin(AcRole::tableName(), 'acr_id = acur_acr_id')) {
            return $this->redirect(\Yii::$app->urlManager->createUrl(self::$masterPage));
        }

        $searchModel = new AcRoleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'user' => $user,
        ]);
    }

    /**
     * Displays a single AcUserRole model.
     * @param integer $id
     * @return mixed
     * @throws HttpException
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new AcUserRole model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AcUserRole();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->acur_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing AcUserRole model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws HttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->acur_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing AcUserRole model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws HttpException
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }



    /**
     * Add new AcUserRole model.
     * If creation is successful, the browser will be show the ajax 'add' page.
     * @param integer $id
     * @return mixed
     */
    public function actionAdd($user_id, $acr_id)
    {
        if ($model = AcUserRole::find()->where(['acur_user_id' => $user_id, 'acur_acr_id' => $acr_id, ])->one()) {
            return $this->renderAjax('add', [
                'save' => true,
                'model' => $model,
            ]);
        } else {
            $model = new AcUserRole();
            $model->acur_user_id = $user_id;
            $model->acur_acr_id = $acr_id;
            if ($model->save()) {
                return $this->renderAjax('add', [
                    'save' => true,
                    'model' => $model,
                ]);
            } else {
                return $this->renderAjax('add', [
                    'save' => false,
                    'model' => $model,
                ]);
            }
        }
    }


    /**
     * Delete exists AcRoleFunc model.
     * If delete is successful, the browser will be show the ajax 'add' page.
     * @param $user_id
     * @param $acr_id
     * @return mixed
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionRemove($user_id, $acr_id)
    {
        if ($model = AcUserRole::find()->where(['acur_user_id' => $user_id, 'acur_acr_id' => $acr_id, ])->one()) {
            if ($model->delete()) {
                return $this->renderAjax('add', [
                    'save' => true,
                    'model' => null,
                ]);
            } else {
                return $this->renderAjax('add', [
                    'save' => false,
                    'model' => $model,
                ]);
            }
        } else {
            return $this->renderAjax('add', [
                'save' => false,
                'model' => null,
            ]);
        }
    }

    public function actionUpdateRole($userId, $roleId)
    {
        $userRole = new AcUserRole();
        return $userRole->updateRole($userId, $roleId);
    }

    /**
     * Finds the AcUserRole model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AcUserRole the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AcUserRole::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }
}
