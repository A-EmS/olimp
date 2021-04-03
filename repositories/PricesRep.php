<?php
namespace app\repositories;

use app\models\Prices;
use Yii;

class PricesRep extends Prices
{
    public static function checkDuplicateByPriceList($priceListId, $exceptedId = null)
    {
        $item = self::findOne(['price_list_id' => $priceListId]);
        return ($item !== null && $item->id != $exceptedId && !empty($item->id));
    }

    public static function addProjectPart(int $projectPartId)
    {
        $priceListIdModels= self::find()->select(['price_list_id'])->distinct()->all();
        foreach ($priceListIdModels as $priceListIdModel) {
            $model = new Prices();
            $model->price_list_id = $priceListIdModel->price_list_id;
            $model->project_part_id = $projectPartId;

            $model->create_user = Yii::$app->user->identity->id;
            $model->create_date = date('Y-m-d H:i:s', time());
            $model->save(false);
        }
    }

    public static function cleanOutPriceFromProjectPart(int $projectPartId)
    {
        self::deleteAll(['project_part_id' => $projectPartId]);
    }
}