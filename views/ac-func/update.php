<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\AcFunc */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => Yii::t('app', 'Ac Func'),
]) . ' ' . $model->acf_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ac Funcs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->acf_id, 'url' => ['view', 'id' => $model->acf_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="ac-func-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
