<?php

namespace app\controllers;

use app\helpers\CSVDocumentGenerator;
use app\models\FinanceClasses;
use app\repositories\DocumentsStatusesRep;
use creocoder\nestedsets\NestedSetsBehavior;
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


    public function actionGetAllByFilter($filters = null)
    {
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
                $whereString .= ' AND targetTable.report_period = \''.(new \DateTime())->format('Y-m-01').'\' ';
            }

            if (isset($filters['ownCompanyIds']) && !empty($filters['ownCompanyIds'])) {
                $ocInString = implode(',', $filters['ownCompanyIds']);
                $whereString .= ' AND targetTable.own_company_id IN ('.$ocInString.') ';
            }

            if (isset($filters['financeClassIds']) && !empty($filters['financeClassIds'])) {
                $fInString = implode(',', $filters['financeClassIds']);
                $whereString .= ' AND targetTable.finance_class_id IN ('.$fInString.') ';
            }
        } else {
            if (empty($filters['report_period'][0]) && empty($filters['report_period'][1])) {
                $whereString .= ' AND targetTable.report_period = \''.(new \DateTime())->format('Y-m-01').'\' ';
            }
        }

        Yii::$app->db->createCommand('SET sql_mode = \'\'')->query();
        $sql = 'SELECT targetTable.id, targetTable.finance_class_id, targetTable.report_period, targetTable.own_company_id, cr.currency_name as currency, fc.name as finance_class,
                 targetTable.amount 
                FROM orders as targetTable 
                left join currencies cr ON (cr.id = targetTable.currency_id)
                left join finance_classes fc ON (fc.id = targetTable.finance_class_id)
                where '.$whereString.'
                ';

        $paid = DocumentsStatusesRep::STATUS_PAID;
        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":ds_id",$paid);
        $items = $command->queryAll();

        $initial = $this->setInitialData($filters);
        $preparedData = $this->prepareData($items, $initial['rows']);
        $parentSections = $this->constructParentSections($preparedData);
        $items = $this->itemsForReport($parentSections, $preparedData);
        $items = $this->addParamsToItems($items);

        $reportCsvFilePath = false;
        if (isset($filters['saveReportToFile']) && !empty($filters['saveReportToFile'])) {
            $reportCsvFilePath = $this->generateReportFile($initial['fields'], $items, 'ConsolidatedReport.csv');
        }

        return json_encode(['items'=> $items, 'fields' => $initial['fields'], 'reportCsvFilePath' => $reportCsvFilePath]);
    }

    private function setInitialData(array $filters): array
    {
        $rows = [];
        $fieldsDateRange = [];
        $fields = ['№' => '№', 'financeClass' => 'Finance Class'];

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
            $childRow['depth'] = $сhild->depth;
            $rows[$сhild->id] = $childRow;
        }

        return ['fields' => $fields, 'rows' => $rows];
    }

    private function prepareData(array $selectedItems, array $initialRows): array
    {
        foreach ($selectedItems as $selectedItem) {
            $reportPeriod = (new \DateTime($selectedItem['report_period']))->format('m.Y');
            $initialRows[$selectedItem['finance_class_id']][$reportPeriod] += $selectedItem['amount'];
        }

        foreach ($initialRows as $key => $initialRow) {
            unset($initialRow['№'], $initialRow['financeClass'], $initialRow['total'], $initialRow['depth']);
            $initialRows[$key]['total'] = array_sum($initialRow);
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
        $pRow['childList'] = $mainChildListIds;
        $pRow['depth'] = $node->depth;
        $pRow['parent'] = 0;
        $parentsResults[$pRow['№']] = $pRow;

        return $parentsResults;
    }

    private function itemsForReport(array $parentSections, array $preparedData): array
    {

        $mainParent = $parentSections[1];
        $report = $this->createReport($mainParent, $parentSections, $preparedData);
        $mainParent['№'] = '';

        return $report;
    }

    private function addParamsToItems(array $items): array
    {

        return $items;
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

            $financeClass = $item['financeClass'];
            $number = $item['№'];

            unset($item['depth'], $item['childList'], $item['parent'], $item['financeClass'], $item['№']);

            $item = array('№' => $number, 'financeClass' => $financeClass) + $item;

            return $item;
        } , $data);

        array_unshift($data, $headFields);
        $csvGenerator = new CSVDocumentGenerator();
        $filePath = $csvGenerator->createFile($data, $fileName);

        return $filePath;
    }

    private function arrayTotal($a, $b) {
        unset($a['№'], $a['financeClass']);
        unset($b['№'], $b['financeClass']);
        
        $array = array();

        foreach ($a as $key => $value) {
            if(isset($b[$key]))
                $array[$key] = $value + $b[$key];
            else $array[$key] = $value ;
        }

        return $array;
    }
}
