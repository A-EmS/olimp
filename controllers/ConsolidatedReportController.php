<?php

namespace app\controllers;

use app\helpers\CSVDocumentGenerator;
use app\models\FinanceClasses;
use app\models\InterfaceVocabularies;
use app\repositories\CurrencyExchangeRatesRep;
use app\repositories\DocumentsStatusesRep;
use app\repositories\PaymentOperationsTypesRep;
use app\repositories\PaymentTypesRep;
use creocoder\nestedsets\NestedSetsBehavior;
use DateTime;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;

class ConsolidatedReportController extends BaseController
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

    protected $errors = [];

    public function actionGetAllByFilter($filters = null)
    {
        $this->errors = [];

        $filters = Yii::$app->request->post('filters', []);

        $whereString = ' targetTable.id > 0 AND document_status_id = :ds_id ';
        if (!empty($filters)) {
            if (isset($filters['report_period'][0]) && !empty($filters['report_period'][0])) {
                $whereString .= ' AND targetTable.report_period >= \''.$filters['report_period'][0].'\' ';
            }
            if (isset($filters['report_period'][1]) && !empty($filters['report_period'][1])) {
                $whereString .= ' AND targetTable.report_period <= \''.$filters['report_period'][1].'\' ';
            }
            if (empty($filters['report_period'][0]) && empty($filters['report_period'][1])) {
                $whereString .= ' AND targetTable.report_period >= \''.(new \DateTime())->format('Y-m-01').'\' ';
            }

            if (isset($filters['ownCompanyIds']) && !empty($filters['ownCompanyIds'])) {
                $ocInString = implode(',', $filters['ownCompanyIds']);
                $whereString .= ' AND targetTable.own_company_id IN ('.$ocInString.') ';
            }

            if (isset($filters['financeClassIds']) && !empty($filters['financeClassIds'])) {
                $fInString = implode(',', $filters['financeClassIds']);
                $whereString .= ' AND targetTable.finance_class_id IN ('.$fInString.') ';
            }

            if (isset($filters['paymentTypeIds']) && !empty($filters['paymentTypeIds'])) {
                $pInString = implode(',', $filters['paymentTypeIds']);
                $whereString .= ' AND targetTable.payment_type_id IN ('.$pInString.') ';
            }
        } else {
            if (empty($filters['report_period'][0]) && empty($filters['report_period'][1])) {
                $whereString .= ' AND targetTable.report_period >= \''.(new \DateTime())->format('Y-m-01').'\' ';
            }
        }

        Yii::$app->db->createCommand('SET sql_mode = \'\'')->query();
        $sql = 'SELECT targetTable.id, targetTable.finance_class_id, targetTable.report_period, targetTable.own_company_id, cr.id as currency_id, cr.currency_name as currency, fc.name as finance_class,
                 targetTable.amount, po_types.name as payment_operation_type_name
                FROM orders as targetTable 
                left join currencies cr ON (cr.id = targetTable.currency_id)
                left join finance_classes fc ON (fc.id = targetTable.finance_class_id)
                left join payment_operation_types po_types ON (po_types.id = fc.payment_operation_type_id)
                where '.$whereString.'
                ';

        $paid = DocumentsStatusesRep::STATUS_PAID;
        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":ds_id",$paid);
        $items = $command->queryAll();

        $initial = $this->setInitialData($filters);
        $preparedData = $this->prepareData($items, $initial['rows']);
        $parentSections = $this->constructParentSections($preparedData);
        $totalsRows = $this->totalsRows($parentSections);
        $items = $this->itemsForReport($parentSections, $preparedData);
        $items = array_merge($items, $totalsRows);


        $reportCsvFilePath = false;
        if (isset($filters['saveReportToFile']) && !empty($filters['saveReportToFile'])) {
            $reportCsvFilePath = $this->generateReportFile($initial['fields'], $items, 'ConsolidatedReport.csv');
        }

        return json_encode(['items'=> $items, 'fields' => $initial['fields'], 'errors'=> $this->errors, 'reportCsvFilePath' => $reportCsvFilePath]);
    }

    private function setInitialData(array $filters): array
    {
        $rows = [];
        $fieldsDateRange = [];
        $fields = ['№' => '№', 'financeClass' => 'Finance Class', 'paymentOperationTypeName' => 'Payment Operation Type'];

        if (isset($filters['report_period'][0]) && !empty($filters['report_period'][0])) {
            $beginRange = new \DateTime($filters['report_period'][0]);
            $endRange = new \DateTime($filters['report_period'][1]);

            while ($beginRange <= $endRange) {
                $fieldsDateRange[$beginRange->format('m.Y')] = $beginRange->format('m.Y');
                $beginRange->modify('first day of next month');
            }
        } else {
            $fieldsDateRange[(new \DateTime())->format('m.Y')] = (new \DateTime())->format('m.Y');
        }

        $fields = array_merge($fields, $fieldsDateRange);
        $fields['total'] = 'Total';

        $node = FinanceClasses::findOne(1);
        $сhildren = $node->leaves()->all();

        /** @var FinanceClasses $сhild */
        foreach ($сhildren as $сhild) {
            $childRow = $fields;
            $childRow = array_fill_keys(array_keys($childRow), 0);
            $childRow['№'] = $сhild->id;
            $childRow['financeClass'] = $сhild->name;
            $childRow['paymentOperationTypeName'] = PaymentOperationsTypesRep::getNameById($сhild->payment_operation_type_id);
            $childRow['paymentOperationTypeId'] = $сhild->payment_operation_type_id;
            $childRow['depth'] = $сhild->depth;
            $rows[$сhild->id] = $childRow;
        }

        return ['fields' => $fields, 'rows' => $rows];
    }

    private function prepareData(array $selectedItems, array $initialRows): array
    {

        $filters = Yii::$app->request->post('filters', []);
        $currencyId = !empty($filters['currencyId']) ? $filters['currencyId'] : 0;

        $exchangeRatesCollection = CurrencyExchangeRatesRep::find()
            ->select(['currency_id_base', 'currency_id_ref', 'rate_ref', 'rate_base'])
            ->where(['currency_id_ref' => $currencyId])
            ->all();

        $exchangeRates = [];
        /** @var CurrencyExchangeRatesRep $item */
        foreach ($exchangeRatesCollection as $item){
            $exchangeRates[$item->currency_id_base] = [
                'currency_id_base' => $item->currency_id_base,
                'currency_id_ref' => $item->currency_id_ref,
                'rate_base' => $item->rate_base,
                'rate_ref' => $item->rate_ref,
            ];
        }

        foreach ($selectedItems as $selectedItem) {

            if (!empty($exchangeRates[$selectedItem['currency_id']])) {
                $exchangeRate = $exchangeRates[$selectedItem['currency_id']]['rate_ref'];
            } else if ($selectedItem['currency_id'] === $currencyId)
                $exchangeRate = 1;
            else {
                $exchangeRate = 1;
                $this->errors['lackOfCurrencyRate'] = true;
            }

            $reportPeriod = (new \DateTime($selectedItem['report_period']))->format('m.Y');
            $ratedAmount = $selectedItem['amount'] * $exchangeRate;
            $initialRows[$selectedItem['finance_class_id']][$reportPeriod] += round($ratedAmount, 2);
        }

        foreach ($initialRows as $key => $initialRow) {
            unset($initialRow['№'], $initialRow['financeClass'], $initialRow['paymentOperationTypeName'], $initialRow['paymentOperationTypeId'], $initialRow['total'], $initialRow['depth']);
            $initialRows[$key]['total'] = round(array_sum($initialRow), 2) ;
        }

        return $initialRows;
    }

    private function constructParentSections(array $preparedDataRows):array
    {
        $parentsResults = [];
        /** @var NestedSetsBehavior $node */
        $node = FinanceClasses::findOne(1);
        $children = $node->children()->orderBy(['priority' => 'ASC'])->all();

        /** @var NestedSetsBehavior $child */
        $pRow = [];
        $mainChildListIds = [];
        foreach ($children as $child) {
            if (!$child->isLeaf()) {
                
                $sRow = [];
                $childListIds = [];
                $childParentId = $child->parents(1)->one()->id;
                $leavesChild = $child->children()->orderBy(['priority' => 'ASC'])->all();

                foreach ($leavesChild as $leaf) {
                    if (isset($preparedDataRows[$leaf->id])){
                        $sRow = $this->arrayTotal($preparedDataRows[$leaf->id], $sRow);
                    }

                    if ($leaf->parents(1)->one()->id === $child->id) {
                        $childListIds[] = $leaf->id;
                    }
                }

                $sRow['№'] = $child->id;
                $sRow['financeClass'] = $child->name;
                $sRow['paymentOperationTypeName'] = PaymentOperationsTypesRep::getNameById($child->payment_operation_type_id);
                $sRow['paymentOperationTypeId'] = $child->payment_operation_type_id;
                $sRow['childList'] = $childListIds;
                $sRow['depth'] = $child->depth;
                $sRow['parent'] = $childParentId;
                $parentsResults[$sRow['№']] = $sRow;
                
            } else {
                $pRow = $this->arrayTotal($preparedDataRows[$child->id], $pRow);
            }

            if ($child->parents(1)->one()->id === $node->id) {
                $mainChildListIds[] = $child->id;
            }
        }

        $pRow['№'] = $node->id;
        $pRow['financeClass'] = $node->name;
        $pRow['paymentOperationTypeName'] = PaymentOperationsTypesRep::getNameById($node->payment_operation_type_id);
        $pRow['paymentOperationTypeId'] = $node->payment_operation_type_id;
        $pRow['childList'] = $mainChildListIds;
        $pRow['depth'] = $node->depth;
        $pRow['parent'] = 0;
        $parentsResults[$pRow['№']] = $pRow;

        return $parentsResults;
    }

    private function totalsRows(array $parentsResults):array
    {
        $totalsAmount = [];
        foreach ($parentsResults as $parentsResult) {
            if ($parentsResult['depth'] === 1) {
                if (empty($totalsAmount[$parentsResult['paymentOperationTypeId']])) {
                    $totalsAmount[$parentsResult['paymentOperationTypeId']] = $this->arrayTotal($parentsResult, []);
                } else {
                    $totalsAmount[$parentsResult['paymentOperationTypeId']] = $this->arrayTotal($totalsAmount[$parentsResult['paymentOperationTypeId']], $parentsResult);
                }
            }
        }

        $income = $totalsAmount[PaymentOperationsTypesRep::INCOME_ID];
        $expense = $totalsAmount[PaymentOperationsTypesRep::EXPENSE_ID];
        $totals = $this->arrayDiff($expense, $income);

        $income = [
                '№'=> ' --- ',
                'financeClass' => PaymentOperationsTypesRep::findOne(PaymentOperationsTypesRep::INCOME_ID)->name,
                'paymentOperationTypeId' => PaymentOperationsTypesRep::INCOME_ID,
                'depth' => 1,
                'specialClass' => 'greatIncome'
            ]
            +
            $income;

        $expense = [
                '№'=> ' --- ',
                'financeClass' => PaymentOperationsTypesRep::findOne(PaymentOperationsTypesRep::EXPENSE_ID)->name,
                'paymentOperationTypeId' => PaymentOperationsTypesRep::EXPENSE_ID,
                'depth' => 1,
                'specialClass' => 'greatExpense'
            ]
            +
            $expense;

        $totals = ['№'=> ' --- ', 'financeClass' => 'ИТОГО', 'paymentOperationTypeId' => 3, 'depth' => 1, 'specialClass' => 'greatTotals'] + $totals;

        return [$income, $expense, $totals];
    }



    private function itemsForReport(array $parentSections, array $preparedData): array
    {

        $mainParent = $parentSections[1];
        $report = $this->createReport($mainParent, $parentSections, $preparedData);
        $mainParent['№'] = '';

        return $report;
    }

    private function createReport(array $parentSection, array $parentSections, array $preparedData, array $levels = []): array
    {
        $items = [];
        if (empty($levels)) {
            $levels = [1];
        }

        $currentSectionLevel = $levels;

        $maxLevel = end($levels);
        $i = 0;
        foreach ($parentSection['childList'] as $childId){
            $i++;
            if (isset($parentSections[$childId])) {
                $item = $parentSections[$childId];
                $item['№'] = $maxLevel;
                if ($item['depth'] === 1) {
                    $levels = [$maxLevel];
                } else {
                    $levels[] = $i;
                    $item['№'] = join('.', $currentSectionLevel).'.'.$i;
                }
                $items[] = $item;
                $maxLevel++;
                $reportItems = $this->createReport($parentSections[$childId], $parentSections, $preparedData, $levels);
                foreach ($reportItems as $reportItem) {
                    $items[] = $reportItem;
                }
            } else {
                $item = $preparedData[$childId];
                if ($item['depth'] === 1) {
                    $item['№'] = $i;
                } else {
                    $item['№'] = join('.', $currentSectionLevel).'.'.$i;
                }
                $maxLevel = $i + 1;
                $items[] = $item;
            }
        }

        return $items;
    }

    private function generateReportFile(array $headFields, array $data, string $fileName): string
    {
        $data = array_map(function ($item){

            $financeClass = $item['financeClass'] ?? '';
            $paymentOperationTypeName = $item['paymentOperationTypeName'] ?? '';
            $number = $item['№'] ?? '';

            unset($item['depth'], $item['childList'], $item['parent'], $item['financeClass'], $item['paymentOperationTypeName'], $item['paymentOperationTypeId'], $item['№'], $item['specialClass']);

            $item = array('№' => $number, 'financeClass' => $financeClass, 'paymentOperationTypeName' => $paymentOperationTypeName) + $item;

            return $item;
        } , $data);

        array_unshift($data, $headFields);
        $csvGenerator = new CSVDocumentGenerator();
        $filePath = $csvGenerator->createFile($data, $fileName);

        return $filePath;
    }

    private function arrayTotal($a, $b) {
        foreach ($a as $k => $v) {
            if ($k !== 'total' && DateTime::createFromFormat('m.Y', $k) === false) {
                unset($a[$k]);
                unset($b[$k]);
            }
        }

        $array = array();

        foreach ($a as $key => $value) {
            if(isset($b[$key]))
                $array[$key] = round(($value + $b[$key]), 2);
            else $array[$key] = round($value, 2) ;
        }

        return $array;
    }

    private function arrayDiff($b, $a) {
        foreach ($a as $k => $v) {
            if ($k !== 'total' && DateTime::createFromFormat('m.Y', $k) === false) {
                unset($a[$k]);
                unset($b[$k]);
            }
        }

        $array = array();

        foreach ($a as $key => $value) {
            if(isset($b[$key]))
                $array[$key] = round(($value - $b[$key]), 2);
            else $array[$key] = 0 - round($value, 2) ;
        }

        return $array;
    }
}
