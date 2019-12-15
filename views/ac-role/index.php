<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\AcRoleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Ac Roles');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'acl'), 'url' => ['site/acl']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ac-role-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Ac Role'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],
            ['class' => 'yii\grid\ActionColumn',
                'visibleButtons' => [
                    'view' => function ($model, $key, $index) {
                        return false;
                     }
                ]
            ],

            'acr_id',
            'acr_name',
            'acr_desc',
            'acr_create_user',
            'acr_create_time',
            'acr_create_ip',
            'acr_update_user',
            'acr_update_time',
            'acr_update_ip',
        ],
    ]); ?>
    <hr />
    <h3>Управление доступами будет открыто после создания роли</h3>
</div>
