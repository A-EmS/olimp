<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\AcRoleFunc */

$this->title = Yii::t('app', 'Create Ac Role Func');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'acl'), 'url' => ['site/acl']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ac Role Funcs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ac-role-func-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
