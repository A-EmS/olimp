<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\AcRole */

$this->title = $model->acr_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ac Roles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ac-role-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->acr_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->acr_id], [
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
    ]) ?>

</div>
