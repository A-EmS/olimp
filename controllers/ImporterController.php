<?php

namespace app\controllers;

use app\models\Cities;
use app\models\Regions;
use DateTime;
use Yii;
use yii\web\Controller;

/**
 * AbController implements the CRUD actions for Ab model.
 */
class ImporterController extends Controller
{

//    const COUNTRY_UA_ID = 217;
//    const COUNTRY_RU_ID = 171;
    /**
     * Lists all Ab models.
     * @return mixed
     * @throws \Exception
     */
    public function actionImportCityAndRegions()
    {
        if (($handle = fopen(__DIR__."/../web/ru.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {

                $t[]=$data[0];

                if(isset($data[1])){
                    $arrayRegion[$data[1]][] = $data[0];
                }

            }
            fclose($handle);

            foreach ($arrayRegion as $region => $cities){
                $regionModel = new Regions();
                $regionModel->country_id = self::COUNTRY_RU_ID;
                $regionModel->name = $region;
                $regionModel->create_user = 2;
                $regionModel->create_date = date('Y-m-d H:i:s', time());
                $regionModel->save(false);
                foreach ($cities as $city){
                    $cityModel = new Cities();
                    $cityModel->region_id = $regionModel->id;
                    $cityModel->name = $city;
                    $cityModel->create_user = 2;
                    $cityModel->create_date = date('Y-m-d H:i:s', time());
                    $cityModel->save(false);
                }
            }
//            echo '<br />';
//            echo '<pre>';
//            echo count($t);
//            print_r($t);
        }
    }
}
