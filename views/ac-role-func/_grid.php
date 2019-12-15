<?php

use app\models\AcRoleFunc;

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\AcRoleFuncSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $acRole app\models\AcRole */

    \app\assets\BtnAsset::register($this);

    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn', 'contentOptions' => ['style' => 'width:40px;'], ],

            [
                'class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['style' => 'width:60px;'],
                'template' => '{add}<br>{remove}',
                'buttons' => [
                    'add' =>
                    function($url, $model) use ($acRole) {
                        return Html::button( AcRoleFunc::exists($acRole->acr_id, $model->acf_id) ? '#' : '+',
                                                    [
                                                        'class' => AcRoleFunc::exists($acRole->acr_id, $model->acf_id) ? 'btn btn-success' : 'btn btn-primary',
                                                        'onclick' => '$.post("'.\yii\helpers\Url::toRoute(['/ac-role-func/add', 'acr_id'=>$acRole->acr_id, 'acf_id'=>$model->acf_id]).'",
                                                                            function(data) {
                                                                                $( \'#'.'add_btn_'.$model->acf_id.'\' ).html(data);
                                                                                $( \'#'.'add_btn_'.$model->acf_id.'\' ).removeClass().addClass(\'btn btn-success\');
                                                                            });',
                                                        'id' => 'add_btn_'.$model->acf_id,
                                                    ]
                                           );
                    },
                    'remove' =>
                    function($url, $model) use ($acRole) {
                        return Html::button('-',
                                                    [
                                                        'class' => 'button_border_red',
                                                        'onclick' => '$.post("'.\yii\helpers\Url::toRoute(['/ac-role-func/remove', 'acr_id'=>$acRole->acr_id, 'acf_id'=>$model->acf_id]).'",
                                                                            function(data) {
                                                                                $( \'#'.'add_btn_'.$model->acf_id.'\' ).html(data);
                                                                                $( \'#'.'add_btn_'.$model->acf_id.'\' ).removeClass().addClass(\'btn btn-primary\');
                                                                            });',
                                                    ]
                                           );
                    }

                ],
            ],

            'acf_id',
            'acf_name',
            'acf_controller',
            'acf_action',
        ],
    ]);

?>
