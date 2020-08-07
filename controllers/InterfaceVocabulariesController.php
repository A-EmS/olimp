<?php

namespace app\controllers;

use app\models\InterfaceVocabularies;
use Yii;
use yii\filters\VerbFilter;


class InterfaceVocabulariesController extends BaseController
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
                    'logout' => ['post'],
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

    public function actionGetInterfaceVocabulary(string $language = 'lang_en') : string
    {

        if (!empty(Yii::$app->request->post('language'))){
            $language = Yii::$app->request->post('language');
        }


        $langOption = 'lang_'.$language;
        $vocabularies = InterfaceVocabularies::findBySql('Select lang_en, '.$langOption.' from interface_vocabularies')->all();

        $langArray = [];
        foreach ($vocabularies as $vocabulary){
            $langArray[$vocabulary['lang_en']] = $vocabulary[$langOption];
        }

        return (json_encode($langArray));
    }

    public function actionGetInterfaceVocabularies() : string
    {

        $notFilledOnly = Yii::$app->request->get('notFilledOnly');

        $additionalWhereString = '';
        if (isset($notFilledOnly) && $notFilledOnly == 1){
            $additionalWhereString = 'where 
                                        lang_ru is NULL ||
                                        lang_ru = \'\'
                                        ';
        }

        $sql = 'SELECT *  
                FROM interface_vocabularies iv 
                 '.$additionalWhereString;

        $items = Yii::$app->db->createCommand($sql)->queryAll();

        return json_encode(['items'=> $items]);

    }

    /**
     * @return false|string
     * @throws \yii\db\Exception
     */
    public function actionGetById($id)
    {
        if ($id == null){
            $id = (int)Yii::$app->request->get('id');
        }

        $sql = 'SELECT * 
                FROM interface_vocabularies iv 
                where id = :id
                ';

        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":id",$id);
        $items = $command->queryOne();

        return json_encode($items);
    }

    public function actionCreate() :int
    {

        $langEn = Yii::$app->request->post('lang_en');
        $sql = 'SELECT count(*) 
                FROM interface_vocabularies
                where lang_en = :lang_en
                ';

        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":lang_en",$langEn);
        $itemsCount = $command->queryScalar();

        if ($itemsCount > 0){
            return $itemsCount;
        }

        try{
            $model = new InterfaceVocabularies();
            $model->lang_en = Yii::$app->request->post('lang_en');
            $model->lang_ru = Yii::$app->request->post('lang_ru');
            $model->save(false);

            return $model->id;
        } catch (\Exception $e){
            return 0;
        }
    }

    public function actionUpdate(int $id = null)
    {
        if ($id == null){
            $id = (int)Yii::$app->request->post('id');
        }

        $model = InterfaceVocabularies::findOne($id);
        $model->lang_en = Yii::$app->request->post('lang_en');
        $model->lang_ru = Yii::$app->request->post('lang_ru');
        $model->save(false);
    }

    public function actionDelete(int $id = null) : string
    {
        if ($id == null){
            $id = (int)Yii::$app->request->post('id');
        }

        $model = InterfaceVocabularies::findOne($id);
        if($model->delete()){
            return json_encode(['status' => true]);
        } else {
            return json_encode(['status' => false]);
        }

    }
}
