<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\AcUserRole */

$this->title = $model->acur_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'acl'), 'url' => ['site/acl']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ac User Roles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ac-user-role-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->acur_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->acur_id], [
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
            'acur_id',
            'acur_user_id',
            'acur_acr_id',
            'acur_create_user',
            'acur_create_time',
            'acur_update_user',
            'acur_update_time',
        ],
    ]) ?>

</div>
