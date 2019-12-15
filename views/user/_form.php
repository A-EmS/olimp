<?php

use app\models\AcRoleSearch;
use app\models\AcRole;
use app\models\AcUserRole;

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php
    $model->user_level = 0;
        $form = ActiveForm::begin([
                'fieldConfig' => [
                    'template' => '<div class="row"><div class="col-sm-2">{label}</div><div class="col-sm-6">{input}</div><div class="col-sm-4">{error}</div></div>',
                ],        
            ]);
    ?>

    <?= $form->field($model, 'user_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'user_pwd')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'user_real')->textInput(['maxlength' => true]) ?>

    <input id="user_level" name="User[user_level]" type="hidden" value="0" />

    <input id="userId" type="hidden" value="<?php echo $model->user_id; ?>" />


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?php
    $searchModelUR = new AcRoleSearch();
    $dataProviderUR = $searchModelUR->search(Yii::$app->request->queryParams);
    ?>

    <?php if(!empty($model->user_id)) { ?>
        <?= $this->render('_grid_user_role', [
            'dataProvider'  => $dataProviderUR,
            'searchModel'   => $searchModelUR,
            'user' => $model,
        ]); ?>
    <?php } else {
        echo '<h3>Роли будут доступны после создания пользователя</h3>';
    } ?>

<div class="modal" id="userRoleModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>Роль пользователя успешно изменена</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>