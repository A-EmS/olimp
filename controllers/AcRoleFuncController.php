<?php

namespace app\controllers;

use app\models\AcRoleFunc;
use app\models\AcFuncSearch;
use app\models\AcRole;
use Yii;
use yii\web\Controller;
use yii\web\HttpException;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * AcRoleFuncController implements the CRUD actions for AcRoleFunc model.
 */
class AcRoleFuncController extends Controller
{
    private static $masterPage = 'ac-role/index';

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
     * Lists all AcRoleFunc models.
     * @return mixed
     */
    public function actionIndex($acr_id) {
        $this->redirect('/');
    }

    /**
     * Displays a single AcRoleFunc model.
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
     * Creates a new AcRoleFunc model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AcRoleFunc();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->acrf_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing AcRoleFunc model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws HttpException
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->acrf_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing AcRoleFunc model.
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
     * Add new AcRoleFunc model.
     * If creation is successful, the browser will be show the ajax 'add' page.
     * @param integer $id
     * @return mixed
     */
    public function actionAdd($acr_id, $acf_id)
    {
        if ($model = AcRoleFunc::find()->where(['acrf_acr_id' => $acr_id, 'acrf_acf_id' => $acf_id, ])->one()) {
            return $this->renderAjax('add', [
                'save' => true,
                'model' => $model,
            ]);
        } else {
            $model = new AcRoleFunc();
            $model->acrf_acr_id = $acr_id;
            $model->acrf_acf_id = $acf_id;
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
     * @param $acr_id
     * @param $acf_id
     * @return mixed
     * @throws \Throwable
     * @throws \yii\db\StaleObjectException
     */
    public function actionRemove($acr_id, $acf_id)
    {
        if ($model = AcRoleFunc::find()->where(['acrf_acr_id' => $acr_id, 'acrf_acf_id' => $acf_id, ])->one()) {
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

    /**
     * Finds the AcRoleFunc model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AcRoleFunc the loaded model
     * @throws NotFoundHttpException
     */
    protected function findModel($id)
    {
        if (($model = AcRoleFunc::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }
}
