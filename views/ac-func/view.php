<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\AcFunc */

$this->title = $model->acf_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'acl'), 'url' => ['site/acl']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ac Funcs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ac-func-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->acf_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->acf_id], [
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
            'acf_id',
            'acf_name',
            'acf_controller',
            'acf_action',
            'acf_create_user',
            'acf_create_time',
            'acf_update_user',
            'acf_update_time',
        ],
    ]) ?>

</div>
