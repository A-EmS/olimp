<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\AcUserRole */

$this->title = Yii::t('app', 'Create Ac User Role');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ac User Roles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ac-user-role-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
