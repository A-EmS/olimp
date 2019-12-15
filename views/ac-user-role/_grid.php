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
                'template' => '{add}<br>{remove}',
                'buttons' => [
                    'add' =>
                    function($url, $model) use ($user) {
                        return Html::button( AcUserRole::exists($user->user_id, $model->acr_id) ? '#' : '+',
                                                    [
                                                        'class' => AcUserRole::exists($user->user_id, $model->acr_id) ? 'btn btn-success' : 'btn btn-primary',
                                                        'onclick' => '$.post("'.\yii\helpers\Url::toRoute(['/ac-user-role/add', 'user_id'=>$user->user_id, 'acr_id'=>$model->acr_id]).'",
                                                                            function(data) {
                                                                                $( \'#'.'add_btn_'.$model->acr_id.'\' ).html(data);
                                                                                $( \'#'.'add_btn_'.$model->acr_id.'\' ).removeClass().addClass(\'btn btn-success\');
                                                                            });',
                                                        'id' => 'add_btn_'.$model->acr_id,
                                                    ]
                                           );
                    },
                    'remove' =>
                    function($url, $model) use ($user) {
                        return Html::button('-',
                                                    [
                                                        'class' => 'button_border_red',
                                                        'onclick' => '$.post("'.\yii\helpers\Url::toRoute(['/ac-user-role/remove', 'user_id'=>$user->user_id, 'acr_id'=>$model->acr_id]).'",
                                                                            function(data) {
                                                                                $( \'#'.'add_btn_'.$model->acr_id.'\' ).html(data);
                                                                                $( \'#'.'add_btn_'.$model->acr_id.'\' ).removeClass().addClass(\'btn btn-primary\');
                                                                            });',
                                                    ]
                                           );
                    }

                ],
            ],

            'acr_name',
            //'acr_create_user',
            //'acr_create_time',
            //'acr_update_user',
            //'acr_update_time',
        ],
    ]); 
    
?>
