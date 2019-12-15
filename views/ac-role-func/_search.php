<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\AcRoleFuncSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ac-role-func-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'acrf_id') ?>

    <?= $form->field($model, 'acrf_acr_id') ?>

    <?= $form->field($model, 'acrf_acf_id') ?>

    <?= $form->field($model, 'acrf_create_user') ?>

    <?= $form->field($model, 'acrf_create_time') ?>

    <?php // echo $form->field($model, 'acrf_update_user') ?>

    <?php // echo $form->field($model, 'acrf_update_time') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
