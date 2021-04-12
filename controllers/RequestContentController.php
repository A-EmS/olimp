<?php

namespace app\controllers;

use app\models\Orders;
use app\models\Prices;
use app\models\Products;
use app\models\RequestLaborCosts;
use app\models\Requests;
use app\repositories\DocumentsStatusesRep;
use app\repositories\FinanceActionsRep;
use app\repositories\PaymentOperationsTypesRep;
use app\repositories\PaymentTypesRep;
use app\repositories\ProductsRep;
use app\repositories\RequestsRep;
use Yii;
use yii\filters\VerbFilter;

class RequestContentController extends BaseController
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
    public function actionGetAllByRequestId($requestId)
    {
        if ($requestId == null){
            $requestId = (int)Yii::$app->request->get('requestId');
        }
        $sql = 'SELECT targetTable.*, r.name as request, pp.part as project_part, pp.code as project_part_code, ps.stage as project_stage,
                uc.user_name as user_name_create, uc.user_id as user_name_create_id, uu.user_name as user_name_update, uu.user_id as user_name_update_id 
                FROM request_labor_costs as targetTable
                left join requests r ON (r.id = targetTable.request_id)
                left join project_parts pp ON (pp.id = targetTable.project_part_id)
                left join project_stages ps ON (ps.id = pp.project_stage_id)
                left join user uc ON (uc.user_id = targetTable.create_user)
                left join user uu ON (uu.user_id = targetTable.update_user)
                where targetTable.request_id = :requestId
                ';

        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":requestId",$requestId);
        $items = $command->queryAll();

        $priceListId = null;

        if (count($items) > 0) {
            $item = current($items);
            $priceListId = $item['price_list_id'];
        }

        return json_encode(['items'=> $items, 'price_list_id'=>$priceListId]);
    }

    public function actionCreateLaborCosts()
    {

        $requestId = Yii::$app->request->post('request_id', 0);
        $priceListId = Yii::$app->request->post('price_list_id', 0);
        $countryId = Yii::$app->request->post('country_id', 0);

        try{
            $count = RequestLaborCosts::find()->select('count(*)')
                ->where('request_id=:requestId')->params([':requestId' => $requestId])->count();

            if ($count <= 0) {

                $sql = 'SELECT targetTable.*
                FROM prices as targetTable
                left join project_parts pp ON (pp.id = targetTable.project_part_id)
                where targetTable.price_list_id = :priceListId and pp.country_id = :countryId
                ';

                $command = Yii::$app->db->createCommand($sql);
                $command->bindParam(":priceListId",$priceListId);
                $command->bindParam(":countryId",$countryId);
                $prices = $command->queryAll();


                foreach ($prices as $price) {
                    $model = new RequestLaborCosts();
                    $model->request_id = $requestId;
                    $model->project_part_id = $price['project_part_id'];
                    $model->price_list_id = $priceListId;
                    $model->status = 1;
                    $model->duration_time_days = 0;
                    $model->cost_for_day = $price['price'];
                    $model->cost_for_all_days = 0;
                    $model->cost_for_offer = 0;
                    $model->notice = null;
                    $model->extra_charge = 0;

                    $model->create_user = Yii::$app->user->identity->id;
                    $model->create_date = date('Y-m-d H:i:s', time());
                    $model->save(false);
                }
            }
        } catch (\Exception $e){
            return 0;
        }
    }

    public function actionUpdateLaborCosts()
    {
        try{
            $items = Yii::$app->request->post('items', []);

                foreach ($items as $item) {
                    $model = RequestLaborCosts::findOne($item['id']);
                    $model->request_id = $item['request_id'];
                    $model->project_part_id = $item['project_part_id'];
                    $model->price_list_id = $item['price_list_id'];
                    $model->status = $item['status'];
                    $model->duration_time_days = $item['duration_time_days'];
                    $model->cost_for_day = $item['cost_for_day'];
                    $model->cost_for_all_days = $item['cost_for_all_days'];
                    $model->cost_for_offer = $item['cost_for_offer'];
                    $model->notice = $item['notice'];
                    $model->extra_charge = $item['extra_charge'];

                    $model->update_user = Yii::$app->user->identity->id;
                    $model->update_date = date('Y-m-d H:i:s', time());
                    $model->save(false);
                }
        } catch (\Exception $e){
            return 0;
        }
    }

    public function actionDelete(int $id = null) : string
    {
        if ($id == null){
            $id = (int)Yii::$app->request->post('id');
        }

        $wp = Requests::findOne($id);

        if($wp->delete()){
            return json_encode(['status' => true]);
        } else {
            return json_encode(['status' => false]);
        }
    }
}