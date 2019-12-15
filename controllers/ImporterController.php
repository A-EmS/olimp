<?php
/**
 * @author Ruslan Bondarenko (Dnipro) r.i.bondarenko@gmail.com
 * @copyright Copyright (C) 2016-2018 Ruslan Bondarenko (Dnipro)
 * @license http://www.yiiframework.com/license/
 */

namespace app\controllers;

use app\models\ApplMain;
use app\models\Person;
use DateTime;
use Yii;
use app\models\Ab;
use app\models\AbSearch;
use yii\web\Controller;
use yii\web\HttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;

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
    public function actionImportUpk()
    {

        $comp_id = 1;
        $reestr = 0;

        if (($handle = fopen(__DIR__."/../web/iupk.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {


                if($data[16] == 'нет в системе'){
                    $applMain = new ApplMain();
                    $applMain->applm_reestr = $reestr;
                    $applMain->applm_prs_id = $data[0]; //физлицо
                    $applMain->applm_position = (!empty($data[1]) ? $data[1] : null); //должность

                    $applMain->applm_svc_id = $data[2]; //услуга
                    $applMain->applm_trp_id = $data[3]; //программа
                    $applMain->applm_ab_id = $data[4]; //юрлицо
                    $applMain->applm_comp_id = $comp_id;
                    $applMain->applm_trt_id = $data[5];
                    $applMain->applm_svdt_id = $data[6];

                    $applMain->applm_date_upk = (new DateTime($data[7]))->format('Y-m-d'); //дата выдачи упк
                    $applMain->applm_apple_date = (new DateTime($data[8]))->format('Y-m-d'); //дата приказа об окончании
                    $applMain->applm_number_upk = $data[9];  //номер упк
                    $applMain->applm_appls_date = (new DateTime($data[10]))->format('Y-m-d'); //дата протокола сдачи теста
                    $applMain->applm_applsx_date = (new DateTime($data[11]))->format('Y-m-d'); //дата ведомости сдачи тестов
                    $applMain->applm_appls0_date = (new DateTime($data[12]))->format('Y-m-d'); //дата ведомости промеж. тестов
                    $applMain->applm_appls0x_date = (new DateTime($data[13]))->format('Y-m-d'); // дата протокола промежуточных тестов
                    $applMain->applm_applcmd_date = (new DateTime($data[14]))->format('Y-m-d');  //дата приказа
                    $applMain->applm_applr_date = (new DateTime($data[15]))->format('Y-m-d'); //дата заявления

                    $applMain->save(false);
                } elseif ($data[16] == 'обновление'){
                    continue;
                } else {
                    $applMain = ApplMain::find()->where("applm_prs_id = $data[0] AND applm_trp_id=$data[3] AND applm_ab_id=$data[4]")->one();
                    if($applMain != null){
                        $applMain->applm_reestr = 0;
                        $applMain->save();
                    }
                }

            }
            fclose($handle);
        }
    }

    /**
     * @throws \Exception
     */
    public function actionUpdateUpkByDocNumber()
    {

        $comp_id = 1;
        $reestr = 0;

        if (($handle = fopen(__DIR__."/../web/iupk.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {

                if($data[16] == 'обновление') {
                    $applMain = ApplMain::find()->where("applm_number_upk = '$data[9]'")->one();
                    if($applMain == null){
                        continue;
                    }
                    $applMain->applm_reestr = $reestr;
                    $applMain->applm_prs_id = $data[0]; //физлицо
                    $applMain->applm_position = (!empty($data[1]) ? $data[1] : null); //должность

                    $applMain->applm_svc_id = $data[2]; //услуга
                    $applMain->applm_trp_id = $data[3]; //программа
                    $applMain->applm_ab_id = $data[4]; //юрлицо
                    $applMain->applm_comp_id = $comp_id;
                    $applMain->applm_trt_id = $data[5];
                    $applMain->applm_svdt_id = $data[6];

                    $applMain->applm_date_upk = (new DateTime($data[7]))->format('Y-m-d'); //дата выдачи упк
                    $applMain->applm_apple_date = (new DateTime($data[8]))->format('Y-m-d'); //дата приказа об окончании
                    $applMain->applm_number_upk = $data[9];  //номер упк
                    $applMain->applm_appls_date = (new DateTime($data[10]))->format('Y-m-d'); //дата протокола сдачи теста
                    $applMain->applm_applsx_date = (new DateTime($data[11]))->format('Y-m-d'); //дата ведомости сдачи тестов
                    $applMain->applm_appls0_date = (new DateTime($data[12]))->format('Y-m-d'); //дата ведомости промеж. тестов
                    $applMain->applm_appls0x_date = (new DateTime($data[13]))->format('Y-m-d'); // дата протокола промежуточных тестов
                    $applMain->applm_applcmd_date = (new DateTime($data[14]))->format('Y-m-d');  //дата приказа
                    $applMain->applm_applr_date = (new DateTime($data[15]))->format('Y-m-d'); //дата заявления

                    $applMain->save(false);
                }
            }
            fclose($handle);
        }
    }

    public function actionPersonImport(){
        if (($handle = fopen(__DIR__."/../web/persons.csv", "r")) !== FALSE) {
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {

                $this->personImport($data[1],$data[2],$data[0]);
            }
            fclose($handle);
        }
    }

    public function personImport($firstName, $secondName, $lastName)
    {
        $fullName = $firstName.' '.$secondName.' '.$lastName;

        $person = Person::find()->where("prs_full_name = '$fullName'")->all();
        if(!empty($person)){
            return $person[0]['prs_id'];
        } else {
            $person = new Person();
            $person->prs_first_name = $firstName;
            $person->prs_middle_name = $secondName;
            $person->prs_last_name = $lastName;
            $person->prs_full_name = $firstName . ' ' . $secondName . ' ' . $lastName;

            $person->save();

            return $person->prs_id;
        }
    }
}
