<?php

namespace app\helpers;

use app\models\Individuals;
use app\models\Patterns;
use app\models\RequestLaborCosts;
use app\models\Requests;
use app\repositories\IndividualsRep;
use Yii;
use yii\base\DynamicModel;
use yii\base\InvalidConfigException;
use yii\data\ArrayDataProvider;
use yii\helpers\ArrayHelper;

class CommercialOffering extends DocumentGenerator
{

    protected $requestId = null;

    protected $priceListId = null;

    protected $patternId = null;

    /**
     * @return null
     */
    public function getRequestId()
    {
        return $this->requestId;
    }

    /**
     * @param null $requestId
     */
    public function setRequestId($requestId)
    {
        $this->requestId = $requestId;
    }

    /**
     * @return null
     */
    public function getPriceListId()
    {
        return $this->priceListId;
    }

    /**
     * @param null $priceListId
     */
    public function setPriceListId($priceListId)
    {
        $this->priceListId = $priceListId;
    }

    /**
     * @return null
     */
    public function getPatternId()
    {
        return $this->patternId;
    }

    /**
     * @param null $patternId
     */
    public function setPatternId($patternId)
    {
        $this->patternId = $patternId;
    }

    public static function getStorage() {
        return Yii::getAlias('@app') . '/web/storage/commercialOffering/';
    }

    public function generate()
    {
        $pattern = Patterns::findOne($this->getPatternId());
        $request = Requests::findOne($this->getRequestId());
        $manager = IndividualsRep::findOne($request->request_manager_individual_id);

        $replacedFileName = str_replace('.docx', '-('. $manager->third_name .').docx', $pattern->filename);
        $this->setFileName($request->id. '-' . $replacedFileName);
        $request->file_name = $this->getFileName();
        $storage = self::getStorage();
        $pathFile = $storage . $this->getFileName();

        $document = new \PhpOffice\PhpWord\TemplateProcessor($pattern->getPatternFilePath());

        if(isset($pathFile) && file_exists($pathFile)){
            chmod($pathFile, 0777);
        }

        $document->setValue('DOC_NUMBER', $request->id);
        $document->setValue('DOC_DATE', $request->date);
        $document->setValue('CUSTOMER_MANAGER_FIO', $manager->full_name);
        $document->setValue('CUSTOMER_MANAGER_CELL', $manager->getPhoneStringForDocuments());
        $document->setValue('CUSTOMER_MANAGER_MAIL', $manager->getEmailStringForDocuments());
        $document->setValue('PROJECT_OBJECT', $request->description);
        $document->setValue('PROJECT_CUSTOMER_DATA', $request->customer_provide);
        $document->setValue('PROJECT_NOTE', $request->notice);
        $document->setValue('PROJECT_EXPERTISE', $request->expertise);

        $rows = RequestLaborCosts::find()
            ->leftJoin('project_parts', 'request_labor_costs.project_part_id = project_parts.id')
            ->leftJoin('project_stages', 'project_parts.project_stage_id = project_stages.id')
            ->select(
                '
                                project_stages.stage as project_stage,
                                project_parts.part as project_part,
                                project_parts.code as code,
                                request_labor_costs.cost_for_offer,
                                request_labor_costs.duration_time_days,
                                request_labor_costs.notice
                            '
            )
            ->where(['request_labor_costs.status' => 1, 'request_labor_costs.request_id' => $request->id, 'request_labor_costs.price_list_id' => $this->getPriceListId()])
            ->orderBy(['priority' => 'ASC'])
            ->createCommand()
            ->queryAll();

        $document->cloneRow('SECTION_NN', count($rows));

        $i = 1;
        $generalCostForOrder = 0;
        $durationTimeDays = 0;
        foreach ($rows as $val) {

            $document->setValue('SECTION_NN#'.$i, $i);
            $document->setValue('PROJECT_STAGE#'.$i, $val['project_stage']);
            $document->setValue('PROJECT_SECTION#'.$i, $val['project_part']);
            $document->setValue('PROJECT_SECTION_SHORT#'.$i, $val['code']);
            $document->setValue('PROJECT_SECTION_SUMM#'.$i, $val['cost_for_offer']);
            $document->setValue('PROJECT_SECTION_EXECUTION_WD#'.$i, $val['duration_time_days']);
            $document->setValue('PROJECT_SECTION_NOTE#'.$i, $val['notice']);

            $generalCostForOrder += $val['cost_for_offer'];
            $durationTimeDays += $val['duration_time_days'];

            $i++;
        }

        $document->setValue('TOTAL_PROJECT_SUMM', number_format($generalCostForOrder, 2, '.', ' '));
        $document->setValue('TOTAL_TIME', number_format($durationTimeDays, 2, '.', ' '));


        /***************************************************/
        Yii::$app->db->createCommand('SET sql_mode = \'\'')->query();
        $rows = RequestLaborCosts::find()
            ->leftJoin('project_parts', 'request_labor_costs.project_part_id = project_parts.id')
            ->leftJoin('project_stages', 'request_labor_costs.project_stage_id = project_stages.id')
            ->leftJoin('request_stage_notices', 'request_stage_notices.request_id = request_labor_costs.request_id AND request_stage_notices.project_stage_id = request_labor_costs.project_stage_id')
            ->select(
                '
                                request_stage_notices.notice as notice,
                                project_stages.stage as project_stage,
                                project_stages.code as stage_code,
                                request_labor_costs.project_stage_duration_time_days as project_stage_duration_time_days,
                                sum(request_labor_costs.cost_for_all_days) as cost_for_all_days_sum,
                                sum(request_labor_costs.cost_for_offer) as cost_for_offer_sum,
                            '
            )
            ->where(['request_labor_costs.status' => 1, 'request_labor_costs.request_id' => $request->id])
            ->orderBy(['priority' => 'ASC'])
            ->groupBy(['request_labor_costs.project_stage_id'])
            ->createCommand()
            ->queryAll();

        $document->cloneRow('STAGE_NN', count($rows));

        $i = 1;
        foreach ($rows as $val) {

            $document->setValue('STAGE_NN#'.$i, $i);
            $document->setValue('PROJECT_STAGE#'.$i, $val['project_stage']);
            $document->setValue('PROJECT_STAGE_SHORT#'.$i, $val['stage_code']);
            $document->setValue('PROJECT_STAGE_EXECUTION_WD#'.$i, $val['project_stage_duration_time_days']);
            $document->setValue('PROJECT_STAGE_SUMM#'.$i, $val['cost_for_offer_sum']);

            $document->setValue('PROJECT_STAGE_NOTE#'.$i, $val['notice']);

            $i++;
        }

        $document->saveAs($pathFile);

        $request->save(false);
    }

    public function download(int $id = null)
    {
        if ($id == null){
            $id = (int)Yii::$app->request->post('id');
        }

        $model = Requests ::findOne($id);

        $file = Yii::getAlias('@app') . '/web/storage/commercialOffering/'.$model->file_name;
        if (file_exists($file)){
            return Yii::$app->response->sendFile($file, $model->file_name);
        } else {
            return false;
        }
    }

}
