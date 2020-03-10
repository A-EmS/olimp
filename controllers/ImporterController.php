<?php

namespace app\controllers;

use app\models\Currencies;
use DateTime;
use Yii;
use yii\web\Controller;

/**
 * AbController implements the CRUD actions for Ab model.
 */
class ImporterController extends Controller
{

    /**
     * Lists all Ab models.
     * @return mixed
     * @throws \Exception
     */
    public function actionImportCityAndRegions()
    {
        if (($handle = fopen(__DIR__."/../web/countries.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {

                $regionModel = new Currencies();
                $regionModel->country_id = $data[0];
                $regionModel->currency_name = $data[1];
                $regionModel->currency_short_name = $data[2];
                $regionModel->sign = $data[3];
                $regionModel->create_user = 19;
                $regionModel->create_date = date('Y-m-d H:i:s', time());
                $regionModel->save(false);

            }
            fclose($handle);

        }
    }
}
