<?php

namespace app\controllers;

use app\models\Agreement;
use app\models\Entity;
use app\models\Person;
use app\models\TrainingProg;
use yii\db\ActiveRecord;
use yii\filters\VerbFilter;
use yii\web\Controller;


class CSVCreatorController extends Controller
{

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => \yii\filters\AccessControl::class,
                'rules' => [
                    [
                        'allow' => false,
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
        ];
    }

    public function actionPers()
    {
        $model = new Person();
        $this->createCSV($model, ['prs_id', 'prs_first_name',  'prs_middle_name',  'prs_full_name', 'prs_inn', 'prs_birth_date', 'prs_pass_sex',
            'prs_pass_serial',
            'prs_pass_number',
            'prs_pass_issued_by',
            'prs_pass_date',
            'prs_connect_link',
            'prs_connect_user',
            'prs_connect_pwd',
            'prs_connect_count',
            ], 'Person.csv');
    }

    public function actionProg()
    {
        $model = new TrainingProg();
        $this->createCSV($model, ['trp_id', 'trp_name', 'trp_code', 'trp_hour'],'TrainingProgram.csv');
    }

    public function actionEnt()
    {
        $model = new Entity();
        $this->createCSV($model, ['ent_id', 'ent_entt_id',  'ent_name',  'ent_name_short',  'ent_orgn',  'ent_inn',   'ent_kpp',   'ent_okpo', ],'Entity.csv');
    }

    public function actionAgr()
    {
        $model = new Agreement();
        $this->createCSV($model, [
            'agr_id',
            'agr_number',
            'agr_date',
            'agr_ab_id',
            'agr_comp_id',
            'agr_acc_id',
            'agr_pat_id',
            'agr_prs_id',
            'agr_sum',
            'agr_tax',
            'agr_ast_id',
            'agr_fdata',
        ], 'Agreement.csv');
    }

    protected function createCSV(ActiveRecord $model, $columns, $fileName){
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="'.$fileName.'"');

        $data = $model::find()->select(join(',', $columns))->limit(3)->all();

        $fp = fopen('php://output', 'wb');
        fputcsv($fp, $columns);
        foreach ( $data as $line ) {

            $valArray = [];
            foreach ($columns as $columnName) {
                $valArray[] = $line->$columnName;
            }

            fputcsv($fp, $valArray);
        }
        fclose($fp);
    }

}
