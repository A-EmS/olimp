<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\AcRoleSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ac-role-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'acr_id') ?>

    <?= $form->field($model, 'acr_name') ?>

    <?= $form->field($model, 'acr_create_user') ?>

    <?= $form->field($model, 'acr_create_time') ?>

    <?= $form->field($model, 'acr_update_user') ?>

    <?php // echo $form->field($model, 'acr_update_time') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
