<?php

namespace app\controllers;

use app\models\Orders;
use app\models\Prices;
use app\models\ProjectData;
use app\models\ProjectParts;
use app\models\ProjectStages;
use app\models\Tills;
use app\repositories\PricesRep;
use app\repositories\TillsRep;
use Yii;
use yii\filters\VerbFilter;

class PricesController extends BaseController
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
     * @param int $id
     * @return false|string
     * @throws \yii\db\Exception
     */
    public function actionGetById($id)
    {
        if ($id == null){
            $id = (int)Yii::$app->request->get('id');
        }

        $sql = 'SELECT targetTable.*
                FROM prices AS targetTable 
                where targetTable.id = :id
                ';

        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":id",$id);
        $items = $command->queryOne();

        return json_encode($items);
    }

    /**
     * @return false|string
     * @throws \yii\db\Exception
     */
    public function actionGetAll()
    {
        Yii::$app->db->createCommand('SET sql_mode = \'\'')->query();
        $sql = 'SELECT targetTable.price_list_id, targetTable.*,pl.name as price_list_name,  cr.currency_name as currency, uc.user_name as user_name_create, uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id 
                FROM prices AS targetTable 
                left join price_lists pl ON (pl.id = targetTable.price_list_id)
                left join currencies cr ON (cr.id = pl.currency_id)
                left join user uc ON (uc.user_id = targetTable.create_user)
                left join user uu ON (uu.user_id = targetTable.update_user)
                group by targetTable.price_list_id
                order by targetTable.id desc
                limit 1000
                ';

        $items = Yii::$app->db->createCommand($sql)->queryAll();

        return json_encode(['items'=> $items]);
    }

    /**
     * @return false|string
     * @throws \yii\db\Exception
     */
    public function actionGetDataByCountryAndPriceList($countryId, $priceListId)
    {

        if ($countryId == null){
            $countryId = (int)Yii::$app->request->get('countryId');
        }

        if ($priceListId == null){
            $priceListId = (int)Yii::$app->request->get('priceListId');
        }

        Yii::$app->db->createCommand('SET sql_mode = \'\'')->query();
        $sql = 'SELECT targetTable.id, targetTable.create_date, targetTable.update_date, targetTable.price, 
                pp.part as project_part_name,pp.priority, pp.code as project_part_code, ps.code as project_stage_code, ps.stage as project_stage_name, uc.user_name as user_name_create, 
                uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id 
                FROM prices AS targetTable 
                left join project_parts pp ON (pp.id = targetTable.project_part_id)
                left join project_stages ps ON (ps.id = pp.project_stage_id)
                left join user uc ON (uc.user_id = targetTable.create_user)
                left join user uu ON (uu.user_id = targetTable.update_user)
                where pp.country_id=:country_id and targetTable.price_list_id=:price_list_id
                order by pp.priority ASC
                limit 1000
                ';

        $items = Yii::$app->db->createCommand($sql)->bindParam(":country_id",$countryId)->bindParam(":price_list_id",$priceListId)->queryAll();

        return json_encode(['items'=> $items]);
    }

    /**
     * @return false|string
     * @throws \yii\db\Exception
     */
    public function actionGetDataByPriceListId($priceListId)
    {

        if ($priceListId == null){
            $priceListId = (int)Yii::$app->request->get('priceListId');
        }

        $sql = 'SELECT targetTable.id, targetTable.price_list_id, targetTable.project_part_id, targetTable.price
                FROM prices AS targetTable 
                where targetTable.price_list_id=:price_list_id
                limit 1000
                ';

        $items = Yii::$app->db->createCommand($sql)->bindParam(":price_list_id",$priceListId)->queryAll();

        return json_encode(['items'=> $items]);
    }

    public function actionCreate()
    {
        if (trim(Yii::$app->request->post('price_list_id')) != '') {
            if (PricesRep::checkDuplicateByPriceList(
                Yii::$app->request->post('price_list_id')
            )
            ){
                $item = Prices::findOne(['price_list_id' => Yii::$app->request->post('price_list_id')]);
                return json_encode(['error' => 'Such price is already exist', 'id' => $item->id]);
            }
        }

        try{
            $priceListId = Yii::$app->request->post('price_list_id');
            foreach (ProjectParts::find()->all() as $projectPart) {
                $model = new Prices();
                $model->price_list_id = $priceListId;
                $model->project_part_id = $projectPart->id;

                $model->create_user = Yii::$app->user->identity->id;
                $model->create_date = date('Y-m-d H:i:s', time());
                $model->save(false);
            }

            return json_encode(['id' => $model->id]);
        } catch (\Exception $e){
            return json_encode(['error'=> $e->getMessage()]);
        }
    }

    public function actionUpdate(int $id = null)
    {

        $updateItem = Yii::$app->request->post('updateItems', []);

        foreach ($updateItem as $item) {
            $model = Prices::findOne($item['id']);
            $model->price = $item['price'];

            $model->update_user = Yii::$app->user->identity->id;
            $model->update_date = date('Y-m-d H:i:s', time());
            $model->save(false);
        }
    }

    public function actionDelete(int $id = null) : string
    {
        if ($id == null){
            $id = (int)Yii::$app->request->post('id');
        }

        if (Prices::deleteAll(['price_list_id' => $id])){
            return json_encode(['status' => true]);
        } else {
            return json_encode(['status' => false]);
        }

    }
}
