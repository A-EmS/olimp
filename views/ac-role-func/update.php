<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\AcRoleFunc */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => Yii::t('app', 'Ac Role Func'),
]) . ' ' . $model->acrf_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ac Role Funcs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->acrf_id, 'url' => ['view', 'id' => $model->acrf_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="ac-role-func-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
