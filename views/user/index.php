<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel app\models\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Users');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'acl'), 'url' => ['site/acl']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Создать', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            [
                'class' => 'yii\grid\SerialColumn',
                'options' => ['width' => Yii::$app->params['yii\grid\SerialColumn']['width']],
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'options' => ['width' => Yii::$app->params['yii\grid\ActionColumn']['width']],
            ],

            'user_id',
            'user_name',
            //'user_pwd',
            'user_real',
            [
                'attribute' => 'user_role',
                'label' => Yii::t('app', 'Ac Roles'),
                'format' => 'raw',
                'value' =>  function ($data) {
//                                $r = Html::tag('div', Html::a(Yii::t('app', 'Change'), ['ac-user-role/index', 'user_id' => $data->user_id], ['class' => 'btn btn-success']));
                                $val = array();
                                foreach ($data->userRoles as $key => $value) {
                                    $val[] = $value->role->acr_name;
                                }
                                return Html::ol($val);
                            },
            ],
//            [
//                'label' => Yii::t('app', 'Ac Roles'),
//                'format' => 'raw',
//                'value' =>  function ($data) {
//                    $roles = array();
//                    foreach ($data->userRoles as $key => $value) {
//                        $roles[] = $value->role->acr_name;
//                    }
//                    return implode(' / ', $roles);
//                },
//            ],
//            'user_level',
//            'user_authKey',
            'user_create_user',
            'user_create_time',
            'user_create_ip',
            'user_update_user',
            'user_update_time',
            'user_update_ip',

            // 'user_accessToken',

        ],
    ]); ?>
<?php Pjax::end(); ?></div>
