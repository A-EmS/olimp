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
        header('Access-Control-Allow-Origin: *');
        $ba = parent::beforeAction($action);

        return $ba;
    }

}
