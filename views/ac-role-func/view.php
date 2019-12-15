<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\AcRoleFunc */

$this->title = $model->acrf_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ac Role Funcs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ac-role-func-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->acrf_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->acrf_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'acrf_id',
            'acrf_acr_id',
            'acrf_acf_id',
            'acrf_create_user',
            'acrf_create_time',
            'acrf_update_user',
            'acrf_update_time',
        ],
    ]) ?>

</div>
