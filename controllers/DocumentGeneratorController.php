<?php

namespace app\controllers;

use app\helpers\CommercialOffering;
use app\helpers\ProjectCalculation;
use app\models\Patterns;
use app\repositories\DocumentTypesRep;
use app\repositories\PatternsRep;
use Yii;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

class DocumentGeneratorController extends BaseController
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

    public function actionGenerate()
    {
        $requestId = (int)Yii::$app->request->post('request_id' , 0);
        $priceListId = (int)Yii::$app->request->post('price_list_id' , 0);
        $documentTypeId = (int)Yii::$app->request->post('document_type_id' , 0);
        $patternId = (int)Yii::$app->request->post('pattern_id' , 0);

        if ($documentTypeId == DocumentTypesRep::SCENARIO_TYPE_COMMERCIAL_OFFERING) {
            $document = new CommercialOffering();
            $document->setPatternId($patternId);
            $document->setPriceListId($priceListId);
            $document->setRequestId($requestId);
        } elseif ($documentTypeId == DocumentTypesRep::SCENARIO_TYPE_PROJECT_CALCULATION) {
            $document = new ProjectCalculation();
            $document->setPatternId($patternId);
            $document->setPriceListId($priceListId);
            $document->setRequestId($requestId);
        }

        $document->generate();

        return json_encode(['fileName' => $document->getFileName()]);
    }

    public function actionDownload(int $id = null, $documentTypeId = null)
    {
        if ($id == null){
            $id = (int)Yii::$app->request->post('id');
        }

        if ($documentTypeId == null){
            $documentTypeId = (int)Yii::$app->request->post('documentTypeId');
        }

        return $this->getDocument($documentTypeId)->download($id);
    }

    protected function getDocument($documentTypeId) {
        if ($documentTypeId == DocumentTypesRep::SCENARIO_TYPE_COMMERCIAL_OFFERING) {
            return new CommercialOffering();
        } else if ($documentTypeId == DocumentTypesRep::SCENARIO_TYPE_PROJECT_CALCULATION) {
            return new ProjectCalculation();
        }
    }
}
