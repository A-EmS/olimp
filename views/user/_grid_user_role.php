<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\AcUserRole;

/* @var $this yii\web\View */
/* @var $searchModel app\models\AcUserRoleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $user app\models\User */

    \app\assets\BtnAsset::register($this);

    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn', 'contentOptions' => ['style' => 'width:40px;'], ],

            [
                'class' => 'yii\grid\ActionColumn',
                'contentOptions' => ['style' => 'width:60px;'],
                'template' => '{add}{remove}',
                'buttons' => [
                    'add' =>
                    function($url, $model) use ($user) {
                        if(AcUserRole::exists($user->user_id, $model->acr_id)){
                            return '';
                        }
                        return Html::button( '<span class="glyphicon glyphicon-plus"></span>',
                                                    [
                                                        'class' => AcUserRole::exists($user->user_id, $model->acr_id) ? 'btn btn-success' : 'btn btn-primary',
                                                        'onclick' => '$.post("'.\yii\helpers\Url::toRoute(['/ac-user-role/add', 'user_id'=>$user->user_id, 'acr_id'=>$model->acr_id]).'",
                                                                            function(data) {
                                                                                $( \'#'.'add_btn_'.$model->acr_id.'\' ).hide();
                                                                                $( \'#'.'del_btn_'.$model->acr_id.'\' ).show();
                                                                                window.location.reload();
                                                                            });',
                                                        'id' => 'add_btn_'.$model->acr_id,
                                                    ]
                                           );
                    },
                    'remove' =>
                    function($url, $model) use ($user) {
                        if(!AcUserRole::exists($user->user_id, $model->acr_id)){
                            return '';
                        }
                        return Html::button('<span class="glyphicon glyphicon-repeat gly-spin"></span>',
                                                    [
                                                        'class' => 'btn btn-danger',
                                                        'onclick' => '$.post("'.\yii\helpers\Url::toRoute(['/ac-user-role/remove', 'user_id'=>$user->user_id, 'acr_id'=>$model->acr_id]).'",
                                                                            function(data) {
                                                                                $( \'#'.'add_btn_'.$model->acr_id.'\' ).show();
                                                                                $( \'#'.'del_btn_'.$model->acr_id.'\' ).hide();
                                                                                window.location.reload();
                                                                            });',
                                                        'id' => 'del_btn_'.$model->acr_id,
                                                    ]
                                           );
                    }

                ],
            ],

            [
                'attribute' => 'acr_name',
                'filter' => false,
            ],
            //'acr_create_user',
            //'acr_create_time',
            //'acr_update_user',
            //'acr_update_time',
        ],
    ]); 
    
?>
