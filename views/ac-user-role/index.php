<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\AcUserRoleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $user app\models\User */

$this->title = Yii::t('app', 'Ac User Roles');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Users'), 'url' => ['user/index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ac-user-role-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= DetailView::widget([
        'model' => $user,
        'template' => '<tr><th style="width:220px;">{label}</th><td>{value}</td></tr>',
        'attributes' => [
            'user_id',
            'user_name',
        ],
    ]) ?>

    <hr>

    <!--
    <p>
        <?= Html::a(Yii::t('app', 'Create Ac User Role'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    -->


    <?= $this->render('_grid', [
                    'dataProvider'  => $dataProvider,
                    'searchModel'   => $searchModel,
                    'user' => $user,
    ]); ?>

</div>
