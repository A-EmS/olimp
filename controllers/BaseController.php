<?php

namespace app\controllers;

use Yii;
use app\models\Ab;
use app\models\AbSearch;
use yii\web\BadRequestHttpException;
use yii\web\Controller;
use yii\web\HttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

class BaseController extends Controller
{
    /**
     * @inheritdoc
     * @throws BadRequestHttpException
     */
    public function beforeAction($action)
    {
        $request = Yii::$app->request;

        if (!$request->isAjax) {
            $this->getView()->registerJsFile('/js/jQuery.js', ['position' => yii\web\View::POS_HEAD]);
            $this->getView()->registerJsFile('/js/vue.2.5.17.min.js', ['position' => yii\web\View::POS_HEAD]);
            $this->getView()->registerJsFile('/js/axios.0.18.0.min.js', ['position' => yii\web\View::POS_HEAD]);
            $this->getView()->registerJsFile('/js/uiv.0.29.0.min.js', ['position' => yii\web\View::POS_HEAD]);
            $this->getView()->registerJsFile('/js/eventHub.js', ['position' => yii\web\View::POS_HEAD]);
            $this->getView()->registerJsFile('/js/validationMIxin.js', ['position' => yii\web\View::POS_HEAD]);
            $this->getView()->registerJsFile('/js/commonApp.js', ['position' => yii\web\View::POS_HEAD]);
        }

        $ba = parent::beforeAction($action);

        return $ba;
    }

}
