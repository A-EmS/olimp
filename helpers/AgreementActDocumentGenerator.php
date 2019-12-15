<?php

namespace app\helpers;

use app\models\Account;
use app\models\Address;
use app\models\AgreementAct;
use app\models\AgreementActA;
use app\models\AgreementAnnex;
use app\models\AgreementAnnexA;
use app\models\AgreementStatus;
use app\models\Bank;
use app\models\Constant;
use app\models\Entity;
use app\models\Pattern;
use app\models\Person;
use app\models\Staff;
use app\models\Tools;
use Yii;
use yii\base\DynamicModel;
use yii\base\InvalidConfigException;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;

class AgreementActDocumentGenerator extends DocumentGenerator
{
    protected $actId;

    public function __construct($actId)
    {
        $this->actId = $actId;
    }

    public function rtfDocument()
    {

        $model = AgreementAct::findOne($this->actId);
        $pattern = Pattern::findOne($model->act_pat_id);

        $servicesRow = AgreementActA::find()
            ->select('acta_ana_id as ana_id, acta_price as price, acta_tax as tax, acta_qty as qty')
            ->where(['acta_act_id' => $this->actId])->createCommand()
            ->queryAll();

        $grid = [];
        foreach($servicesRow as $k => $serviceRow){
            $ana = AgreementAnnexA::findOne($serviceRow['ana_id']);
            $serviceRow['tax'] = floatval($serviceRow['tax']);
            $serviceRow['ana_name'] =
//                            ($ana->agreementAccAs[0]->acaAga->aga_number ?? '').' *** '.
//                            (isset($ana->anaAgra) ? $ana->anaAgra->agra_number : '') . ' / ' .
                (isset($ana->anaSvc) ? $ana->anaSvc->svc_name : '') . ' / ' .
                (isset($ana->anaTrp) ? $ana->anaTrp->trp_name : '');
            $grid[$k] = $serviceRow;
        }

        $tmpl_name = Yii::getAlias('@app') . '/storage/' . $pattern->pat_fname;
        $file_name = Yii::getAlias('@app') . '/storage/' . $model->act_date . '_' . $pattern->pat_fname;

        if(isset($file_name) && file_exists($file_name)){
            chmod($tmpl_name, 0777);
            chmod($file_name, 0777);
        }

        file_put_contents($tmpl_name, $pattern->pat_fdata);

        $document = new \PhpOffice\PhpWord\TemplateProcessor($tmpl_name);
        $vars = $document->getVariables();

        if (array_search('num', $vars) !== false) {
            $document->cloneRow('num', count($grid));
        }

        $tag_val = $model->act_number;
        $document->setValue('DOC_NUMBER', $tag_val);

        $tag_val = \DateTime::createFromFormat('Y-m-d', $model->act_date)->format('d-m-Y');
        $document->setValue('DOC_DATE', $tag_val);

        $dateVal = \DateTime::createFromFormat('Y-m-d', $model->act_date)->getTimestamp();
        $document->setValue('DOC_DATE_DD', strftime('%d', $dateVal));
        $document->setValue('DOC_DATE_MONTH', Constant::MONTHS[strftime('%B', $dateVal)]);
        $document->setValue('DOC_DATE_YYYY', strftime('%Y', $dateVal));


        $tag_val = '';
        if (!is_null($model->actAgr)) {
            $tag_val = $model->actAgr->agr_number;
        }
        $document->setValue('AGR_NUMBER', $tag_val);

        $tag_val = '';
        if (!is_null($model->actAgr)) {
            $tag_val = \DateTime::createFromFormat('Y-m-d', $model->actAgr->agr_date)->format('d-m-Y');
        }
        $document->setValue('AGR_DATE', $tag_val);

        $dateVal = \DateTime::createFromFormat('Y-m-d', $model->actAgr->agr_date)->getTimestamp();
        $document->setValue('AGR_DATE_DD', strftime('%d', $dateVal));
        $document->setValue('AGR_DATE_MONTH', Constant::MONTHS[strftime('%B', $dateVal)]);
        $document->setValue('AGR_DATE_YYYY', strftime('%Y', $dateVal));

        $tag_val = '';
        if ($obj = Entity::findOne($model->act_ab_id)) {
            $tag_val = $obj->entEntt->entt_name;
        }
        $document->setValue('ORGANISATION_TYPE_FULL', $tag_val);


        $tag_val = '';
        if ($obj = Entity::findOne($model->act_ab_id)) {
            $tag_val = $obj->ent_name;
        }
        $document->setValue('ORGANISATION_NAME_FULL', $tag_val);


        $tag_val = '';
        if ($obj = Address::find()->where(['add_id'=>$model->act_add_id])->one()) {
            $tag_val =
                (!empty($obj->add_index) ? $obj->add_index.', ' : '') .
                (!empty($obj->addCou) ? $obj->addCou->cou_name.', ' : '') .
                (!empty($obj->addReg)? $obj->addReg->reg_name.', ' : '') .
                (!empty($obj->addCity) ? $obj->addCity->city_name.', ' : '') .
                (!empty($obj->add_data) ? $obj->add_data : '');
        }
        $document->setValue('ORGANISATION_U_ADDRESS', $tag_val);

        $tag_val = '';
        if ($obj = Entity::findOne($model->act_ab_id)) {
            $tag_val = $obj->ent_inn;
        }
        $document->setValue('ORGANISATION_INN', $tag_val);

        $tag_val = '';
        if ($obj = Entity::findOne($model->act_ab_id)) {
            $tag_val = $obj->ent_kpp;
        }
        $document->setValue('ORGANISATION_KPP', $tag_val);

        $tag_val = '';
        if ($obj = Account::findOne(['acc_id' => $model->actAgr->agr_acc_id])) {
            $tag_val = $obj->acc_number;
        }
        $document->setValue('ORGANISATION_R_SCHET', $tag_val);


        $tag_val = '';
        if ($acc = Account::findOne(['acc_id' => $model->act_acc_id])) {
            if ($obj = Bank::findOne($acc->acc_bank_id)) {
                $tag_val = $obj->bank_name;
            }
        }
        $document->setValue('ORGANISATION_BANK_NAME', $tag_val);

        $tag_val = '';
        if ($acc = Account::findOne(['acc_id' => $model->act_acc_id])) {
            if ($obj = Bank::findOne($acc->acc_bank_id)) {
                $tag_val = $obj->bank_account;
            }
        }
        $document->setValue('ORGANISATION_BANK_K_SCHET', $tag_val);

        $tag_val = '';
        if ($acc = Account::findOne(['acc_id' => $model->act_acc_id])) {
            if ($obj = Bank::findOne($acc->acc_bank_id)) {
                $tag_val = $obj->bank_bic;
            }
        }
        $document->setValue('ORGANISATION_BANK_BIK', $tag_val);

        $tag_val = '';
        if ($obj = Person::findOne($model->act_prs_id)) {
            $tag_val = $obj->prs_full_name;
        }
        $document->setValue('ORGANISATION_FIO', $tag_val);


        $tag_val = '';
        if ($obj = Staff::find()->where(['stf_ent_id' => $model->act_ab_id, 'stf_prs_id' => $model->act_prs_id])->one()) {
            $tag_val = $obj->stf_position;
        }
        $document->setValue('ORGANISATION_DOLGNOST', $tag_val);

        $qty = 0;
        foreach ($grid as $k => $value) {
            $qty += $value['qty'];
        }
        $tag_val = number_format($qty);
        $document->setValue('DOC_QTY', $tag_val);


        $tag_val = number_format($model->act_sum, 2);
        $document->setValue('DOC_SUM', $tag_val);

        $tag_val = Tools::num2str($model->act_sum);
        $document->setValue('DOC_SUM_TEXT', $tag_val);

        $tag_val = number_format($model->act_tax, 2);
        $document->setValue('DOC_TAX', $tag_val);

        $tag_val = Tools::num2str($model->act_tax);
        $document->setValue('DOC_TAX_TEXT', $tag_val);

        $i = 1;
        foreach ($grid as $k => $val) {
            $tax = 0;
            $sum = 0;
            if (isset($grid[$i-1]['qty'])) {
                if (isset($grid[$i-1]['tax'])) {
                    $tax = $grid[$i-1]['tax']*$grid[$i-1]['qty'];
                }
                if (isset($grid[$i-1]['price'])) {
                    $sum = $grid[$i-1]['price']*$grid[$i-1]['qty'];
                }
            }
            $document->setValue('num#' . $i, $i);
            $document->setValue('SVC#' . $i, isset($grid[$i-1]['ana_name']) ? $grid[$i-1]['ana_name'] : '');
            $document->setValue('PRICE#' . $i, number_format($grid[$i-1]['price'], 2));
            $document->setValue('QTY#' . $i, $grid[$i-1]['qty']);
            $document->setValue('SUM_T#' . $i, $tax==0 ? Yii::t('app', 'Without Tax') : number_format($tax, 2) );
            $document->setValue('SUM#' . $i, number_format($sum, 2));
            $i++;
        }
        $document->saveAs($file_name);

        $model->act_fdata = str_replace('/', '-', $model->act_number).' '.$pattern->pat_fname;
        $model->act_data = file_get_contents($file_name);

        chmod($tmpl_name, 0777);
        chmod($file_name, 0777);
        unlink($file_name);
        unlink($tmpl_name);

        $model->save();

        return '/index.php?r=entity-frm/download-agreement-act&id='.$model->act_id;

    }
}
