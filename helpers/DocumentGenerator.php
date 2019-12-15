<?php

namespace app\helpers;

use app\models\Account;
use app\models\AgreementAct;
use app\models\AgreementAnnex;
use app\models\AgreementAnnexA;
use app\models\AgreementStatus;
use app\models\Bank;
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

class DocumentGenerator
{

    public function MSWordDocumentForAgreementAnnex($agreementId)
    {
            $model = AgreementAnnex::findOne($agreementId);

            $model->agra_ast_id  =  AgreementStatus::find()->orderBy('ast_id')->one()->ast_id;

            $pattern = Pattern::findOne($model->agra_pat_id);

            $tmpl_name = Yii::getAlias('@app') . '/storage/' . $pattern->pat_fname;
            $file_name = Yii::getAlias('@app') . '/storage/' . $model->agra_date . '_' . $pattern->pat_fname;

            if(isset($file_name) && file_exists($file_name)){
                chmod($tmpl_name, 0777);
                chmod($file_name, 0777);
            }

            file_put_contents($tmpl_name, $pattern->pat_fdata);

            $document = new \PhpOffice\PhpWord\TemplateProcessor($tmpl_name);

            $servicesRow = AgreementAnnexA::find()
                ->leftJoin('svc', 'agreement_annex_a.ana_svc_id = svc.svc_id')
                ->leftJoin('training_prog', 'agreement_annex_a.ana_trp_id = training_prog.trp_id')
                ->leftJoin('svc_doc_type', 'svc.svc_id = svc_doc_type.svdt_id')
                ->select(
                    '
                                agreement_annex_a.ana_svc_id as svc_id,
                                
                                svc.svc_name as svc_name, 
                                svc_doc_type.svdt_name as svc_doc,  
                                svc.svc_hour as svc_hour,
                                
                                agreement_annex_a.ana_trp_id as trp_id,
                                training_prog.trp_name as trp_name, 

                                agreement_annex_a.ana_cost_a as cost_a, 
                                agreement_annex_a.ana_price_a as price_a, 
                                agreement_annex_a.ana_price as price, 
                                agreement_annex_a.ana_tax as tax, 
                                agreement_annex_a.ana_qty as qty
                            '
                )
                ->where(['agreement_annex_a.ana_agra_id' => $agreementId])->createCommand()
                ->queryAll();

            $vars = $document->getVariables();
            if (array_search('num', $vars) !== false) {
                $document->cloneRow('num', count($servicesRow));
            }

            $date_id = AgreementAnnex::find()->where(['agra_agr_id' => $model->agra_agr_id])->count('agra_date') + 1;
            $number = $model->agraAgr->agr_number.'-'.$date_id;
            while (AgreementAnnex::find()->where(['agra_agr_id' => $model->agra_agr_id, 'agra_number' => $number])->exists()) {
                $date_id ++;
                $number = $model->agraAgr->agr_number.'-'.$date_id;
            }
            $model->agra_number = $model->agraAgr->agr_number.'-'.$date_id;
            $model->agra_fdata = str_replace('/', '-', $model->agra_number).' '.$pattern->pat_fname;

            $tag_val = $model->agra_number;
            $document->setValue('DOC_NUMBER', $tag_val);

            $tag_val = \DateTime::createFromFormat('Y-m-d', $model->agra_date)->format('d-m-Y');
            $document->setValue('DOC_DATE', $tag_val);


            $tag_val = isset($model->agraAgr) ? $model->agraAgr->agr_number : '';
            $document->setValue('AGR_NUMBER', $tag_val);

            $tag_val = isset($model->agraAgr) ? \DateTime::createFromFormat('Y-m-d', $model->agraAgr->agr_date)->format('d-m-Y') : '';
            $document->setValue('AGR_DATE', $tag_val);

            if ($obj = Entity::findOne($model->agra_ab_id)) {
                $tag_val = $obj->entEntt->entt_name;
                $document->setValue('ORGANISATION_TYPE_FULL', $tag_val);
            }
            if ($obj = Entity::findOne($model->agra_ab_id)) {
                $tag_val = $obj->ent_name;
                $document->setValue('ORGANISATION_NAME_FULL', $tag_val);
            }
            if ($obj = $model->agraAdd) {
                $tag_val =
                    (!empty($obj->add_index) ? $obj->add_index.', ' : '') .
                    (!empty($obj->addCou) ? $obj->addCou->cou_name.', ' : '') .
                    (!empty($obj->addReg)? $obj->addReg->reg_name.', ' : '') .
                    (!empty($obj->addCity) ? $obj->addCity->city_name.', ' : '') .
                    (!empty($obj->add_data) ? $obj->add_data : '');
                $document->setValue('ORGANISATION_U_ADDRESS', $tag_val);
            }
            if ($obj = Entity::findOne($model->agra_ab_id)) {
                $tag_val = $obj->ent_inn;
                $document->setValue('ORGANISATION_INN', $tag_val);
            }
            if ($obj = Entity::findOne($model->agra_ab_id)) {
                $tag_val = $obj->ent_kpp;
                $document->setValue('ORGANISATION_KPP', $tag_val);
            }
            if ($obj = Account::findOne(['acc_id' => $model->agra_acc_id])) {
                $tag_val = $obj->acc_number;
                $document->setValue('ORGANISATION_R_SCHET', $tag_val);
            }
            if ($acc = Account::findOne(['acc_id' => $model->agra_acc_id])) {
                if ($obj = Bank::findOne($acc->acc_bank_id)) {
                    $tag_val = $obj->bank_name;
                    $document->setValue('ORGANISATION_BANK_NAME', $tag_val);
                }
            }
            if ($acc = Account::findOne(['acc_id' => $model->agra_acc_id])) {
                if ($obj = Bank::findOne($acc->acc_bank_id)) {
                    $tag_val = $obj->bank_account;
                    $document->setValue('ORGANISATION_BANK_K_SCHET', $tag_val);
                }
            }
            if ($acc = Account::findOne(['acc_id' => $model->agra_acc_id])) {
                if ($obj = Bank::findOne($acc->acc_bank_id)) {
                    $tag_val = $obj->bank_bic;
                    $document->setValue('ORGANISATION_BANK_BIK', $tag_val);
                }
            }
            if ($obj = Person::findOne($model->agra_prs_id)) {
                $tag_val = $obj->prs_full_name;
                $document->setValue('ORGANISATION_FIO', $tag_val);
            }
            if ($obj = Staff::find()->where(['stf_ent_id' => $model->agra_ab_id, 'stf_prs_id' => $model->agra_prs_id])->one()) {
                $tag_val = $obj->stf_position;
                $document->setValue('ORGANISATION_DOLGNOST', $tag_val);
            }
            if (!empty($model->agra_sum)) {
                $tag_val = number_format($model->agra_sum, 2);
            } else {
                $tag_val = '';
            }
            $document->setValue('DOC_SUM', $tag_val);

            $tag_val = Tools::num2str($model->agra_sum);
            $document->setValue('DOC_SUM_TEXT', $tag_val);

            if (!empty($model->agra_tax)) {
                $tag_val = number_format($model->agra_tax, 2);
            } else {
                $tag_val = '';
            }
            $document->setValue('DOC_TAX', $tag_val);

            $tag_val = Tools::num2str($model->agra_tax);
            $document->setValue('DOC_TAX_TEXT', $tag_val);

            $i = 1;
            foreach ($servicesRow as $val) {
                $tax = isset($val['tax']) && isset($val['qty']) ?  floatval($val['tax'])*floatval($val['qty']) : '';
                $sum = isset($val['price']) && isset($val['qty']) ? floatval($val['price'])*floatval($val['qty']) : '';

                $document->setValue('num#'.$i, $i);
                $document->setValue('SVC#'.$i, $val['svc_name']);
                $document->setValue('PROG#'.$i, $val['trp_name']);

                $document->setValue('DOC#'.$i,      $val['svc_doc']);
                $document->setValue('HOUR#'.$i,     $val['svc_hour']);
                $document->setValue('PRICE#'.$i,    number_format($val['price'], 2));
                $document->setValue('QTY#'.$i,      $val['qty']);
                $document->setValue('SUM_T#'.$i,    $tax==0 ? Yii::t('app', 'Without Tax') : number_format($tax, 2));
                $document->setValue('SUM#'.$i,      number_format($sum, 2));
                $i++;

            }

            $document->saveAs($file_name);

            $model->agra_fdata = str_replace('/', '-', $model->agra_number).' '.$pattern->pat_fname;
            $model->agra_data = file_get_contents($file_name);

            chmod($tmpl_name, 0777);
            chmod($file_name, 0777);
            unlink($file_name);
            unlink($tmpl_name);

            $model->save();

            return '/index.php?r=entity-frm/download-agreement-annex&id='.$model->agra_id;

    }

}
