<?php

namespace app\controllers;

use app\models\AcFunc;
use app\models\AcRoleFunc;
use Yii;
use app\models\AcRole;
use app\models\AcRoleSearch;
use yii\web\Controller;
use yii\web\HttpException;
use yii\filters\VerbFilter;
use yii\web\NotFoundHttpException;

/**
 * AcRoleController implements the CRUD actions for AcRole model.
 */
class AcRoleController extends BaseController
{
    private static $detailsPage = 'ac-role-func/index';

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
                         'matchCallback' =>
                             function ($rule, $action) {
                                 return \app\models\AcAccess::checkAction($action);
                             },
                     ],
                ],
            ],
        ];
    }

    /**
     * Lists all AcRole models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AcRoleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AcRole model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        if ($model->acr_id != 0) {
            return $this->redirect(\Yii::$app->urlManager->createUrl(self::$detailsPage). '&acr_id='.$id );
        }

        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new AcRole model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AcRole();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['update', 'id' => $model->acr_id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing AcRole model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->acr_id]);
        } else {
            return $this->render('update', [
                'model' => $model,
                'acFunctions' => AcFunc::find()->orderBy('acf_name asc')->all(),
                'functionsInRole' => AcRoleFunc::find()->select('acrf_acf_id')->where("acrf_acr_id = $id")->column(),
            ]);
        }
    }

    public function actionUpdateAccess($roleId, $idsForAccess)
    {
        AcRoleFunc::deleteAll("acrf_acr_id = $roleId");

        $idsForAccess = explode(',', $idsForAccess);

        foreach ($idsForAccess as $id){
            $acRoleFunc = new AcRoleFunc();
            $acRoleFunc->acrf_acr_id = $roleId;
            $acRoleFunc->acrf_acf_id = $id;
            $acRoleFunc->save();
        }

        return json_encode(['success' => true]);

    }

    /**
     * Deletes an existing AcRole model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the AcRole model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AcRole the loaded model
     * @throws HttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AcRole::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException(Yii::t('app', 'The requested page does not exist.'));
        }
    }
}
