<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\AcRoleFunc */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ac-role-func-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'acrf_acr_id')->textInput() ?>

    <?= $form->field($model, 'acrf_acf_id')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
