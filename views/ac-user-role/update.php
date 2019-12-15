<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\AcUserRole */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => Yii::t('app', 'Ac User Role'),
]) . ' ' . $model->acur_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ac User Roles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->acur_id, 'url' => ['view', 'id' => $model->acur_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="ac-user-role-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
