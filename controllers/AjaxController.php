<?php

namespace app\controllers;

use app\helpers\DocumentGenerator;
use app\models\AgreementAcc;
use app\models\AgreementAccA;
use app\models\AgreementAct;
use app\models\AgreementActA;
use app\models\AgreementAnnex;
use app\models\AgreementAnnexA;
use Yii;
use yii\web\Controller;

class AjaxController extends Controller
{

    public function actionSaveServiceRow() {

        $anaId = $_POST['anaId'];
        $qty = $_POST['qty'];
        $servicePrice = $_POST['servicePrice'];
        $appPrice = $_POST['appPrice'];
        $appTax = $_POST['appTax'];
        $serviceTax = isset($_POST['serviceTax']) ? $_POST['serviceTax'] : null;

        $serviceRow = AgreementAnnexA::findOne($anaId);
        $serviceRow->setAttribute('ana_price', $servicePrice);
        $serviceRow->setAttribute('ana_qty', $qty);
        $serviceRow->setAttribute('ana_tax', $serviceTax);
        $serviceRow->save();

        $app = AgreementAnnex::findOne($serviceRow->ana_agra_id);
        $app->setAttribute('agra_sum', $appPrice);
        $app->setAttribute('agra_tax', $appTax);
        $app->save();

        $documentGenerator = new DocumentGenerator();
        $documentLink = $documentGenerator->MSWordDocumentForAgreementAnnex($app->agra_id);

        return json_encode(
            [
                'result' => true,
                'qty' => $qty,
                'servicePrice' => $servicePrice,
                'documentLink' => $documentLink,
            ]
        );
    }

    public function actionServiceDelete() {

        $anaId = $_POST['anaId'];
        $appPrice = $_POST['appPrice'];

        $serviceRow = AgreementAnnexA::findOne($anaId);
        $app = AgreementAnnex::findOne($serviceRow->ana_agra_id);

        $app->setAttribute('agra_sum', $appPrice);
        $app->save();

        $serviceRow->delete();

        $documentGenerator = new DocumentGenerator();
        $documentLink = $documentGenerator->MSWordDocumentForAgreementAnnex($app->agra_id);

        return json_encode(
            [
                'result' => true,
                'documentLink' => $documentLink,
            ]
        );

    }

    public function actionServiceRelationDocuments(){
        $serviceId = $_POST['anaId'];
        $resultArray = $this->getServiceRelationDocuments($serviceId);

        if($resultArray != []){
            return json_encode(
                [
                    'result' => true,
                    'message' => 'Услугу в приложении нельзя редактировать тк приложение привязано к акту или счету. <br /> Номера счетов: '.implode(', ', $resultArray['acountsNumbers']).' <br />'.'Номера актов: '.implode(', ', $resultArray['actsNumbers'])
                ]
            );
        } else {
            return json_encode(
                [
                    'result' => false,
                ]
            );
        }

    }

    public function getServiceRelationDocuments($serviceId)
    {
        $resultArray = [];

        $serviceRow = AgreementAnnexA::findOne($serviceId);

        $accountsNumbers = AgreementAcc::find()->innerJoin('agreement_acc_a ac_a', 'ac_a.aca_aga_id = agreement_acc.aga_id')->select('aga_number')->where(['ac_a.aca_ana_id' => $serviceRow->ana_id])->column();
        $actsNumbers = AgreementAct::find()->innerJoin('agreement_act_a act_a', 'act_a.acta_act_id = agreement_act.act_id')->select('act_number')->where(['act_a.acta_ana_id' => $serviceRow->ana_id])->column();

        if(!empty($accountsNumbers) || !empty($actsNumbers)){
            return [
                'acountsNumbers' => $accountsNumbers,
                'actsNumbers' => $actsNumbers
            ];
        }

        return $resultArray;
    }

}
