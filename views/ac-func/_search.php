<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\AcFuncSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ac-func-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'acf_id') ?>

    <?= $form->field($model, 'acf_name') ?>

    <?= $form->field($model, 'acf_controller') ?>

    <?= $form->field($model, 'acf_action') ?>

    <?= $form->field($model, 'acf_create_user') ?>

    <?php // echo $form->field($model, 'acf_create_time') ?>

    <?php // echo $form->field($model, 'acf_update_user') ?>

    <?php // echo $form->field($model, 'acf_update_time') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
