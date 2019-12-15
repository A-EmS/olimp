<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\AcUserRoleSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ac-user-role-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'acur_id') ?>

    <?= $form->field($model, 'acur_user_id') ?>

    <?= $form->field($model, 'acur_acr_id') ?>

    <?= $form->field($model, 'acur_create_user') ?>

    <?= $form->field($model, 'acur_create_time') ?>

    <?php // echo $form->field($model, 'acur_update_user') ?>

    <?php // echo $form->field($model, 'acur_update_time') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
