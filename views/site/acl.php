<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
$this->title = Yii::t('app', 'acl');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-acl">
    <h1><?= Html::encode($this->title) ?></h1>


<?php
echo yii\widgets\Menu::widget([
            'items'=> [
                ['label' => Yii::t('app', 'Ac Funcs'), 'url' => ['/ac-func']],
                ['label' => Yii::t('app', 'Ac Roles'), 'url' => ['/ac-role']],
                //['label' => Yii::t('app', 'V1 Ac Role Funcs'), 'url' => ['/v1-ac-role-func']],
                //['label' => Yii::t('app', 'V1 Ac User Roles'), 'url' => ['/v1-ac-user-role']],
                ['label' => Yii::t('app', 'Users'), 'url' => ['/user']],
            ],

]);
?>

</div>
