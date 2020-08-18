<?php

namespace app\controllers;

use app\models\FinanceDocuments;
use app\models\OwnCompanies;
use app\models\Taxes;
use Yii;
use yii\filters\VerbFilter;

class TaxesController extends BaseController
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
    public function actionGetAll()
    {
        $sql = 'SELECT * 
                FROM taxes
                ';

        $items = Yii::$app->db->createCommand($sql)->queryAll();

        return json_encode(['items'=> $items]);
    }

    /**
     * @return false|string
     * @throws \yii\db\Exception
     */
    public function actionGetForDocumentContent($id = null)
    {
        if ($id == null){
            $id = (int)Yii::$app->request->get('id');
        }

        /** @var FinanceDocuments $document */
        $document = FinanceDocuments::find($id)->one();

        /** @var OwnCompanies $ownCompany */
        $ownCompany = OwnCompanies::find($document->own_company_id)->one();

        /** @var Taxes $tax */
        $tax = Taxes::find($ownCompany->taxes_id)->one();

        return json_encode(['id'=> $tax->id, 'name'=> $tax->name, 'tax_part'=> $tax->tax_part]);
    }
}
