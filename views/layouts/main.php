<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\models\AcUserRole;
use yii\helpers\Html;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\widgets\Breadcrumbs;
use app\assets\AppAsset;

use yii\helpers\Url;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
<body>
<?php $this->beginBody() ?>

<div class="wrap">
    <?php
    $isAdmin = false;
    if(isset(Yii::$app->user->identity)){
        $isAdmin = Yii::$app->user->identity->isAdmin || AcUserRole::find()->where(['acur_user_id' => Yii::$app->user->identity->id, 'acur_acr_id' => 0])->exists();
    }
    NavBar::begin([
        'brandLabel' => '<img src="/img/logos/a-ems.jpg" style="width: 40px;">',
        'brandUrl' => Yii::$app->homeUrl,
        'options' => [
            'class' => 'navbar-inverse navbar-fixed-top',
        ],
    ]);
    try {
        echo Nav::widget([
            'options' => ['class' => 'navbar-nav navbar-right'],
            'items' => [

//                ['label' => Yii::t('app', 'Core'),
//                    'visible' => isset(Yii::$app->user->identity->roles) && count(Yii::$app->user->identity->roles)>0 && !Yii::$app->user->isGuest && (Yii::$app->user->identity->level > 0),
//                    'items' => [
//                        (isset(Yii::$app->user->identity->accessActions['country/index'])|| $isAdmin) ? ['label' => Yii::t('app', 'Countries'), 'url' => ['/country']] : '',
//                        (isset(Yii::$app->user->identity->accessActions['region/index'])|| $isAdmin) ? ['label' => Yii::t('app', 'Regions'), 'url' => ['/region']] : '',
//                        (isset(Yii::$app->user->identity->accessActions['city/index'])|| $isAdmin) ? ['label' => Yii::t('app', 'Cities'), 'url' => ['/city']] : '',
//                        (isset(Yii::$app->user->identity->accessActions['city/index']) || $isAdmin) ? '<li class="divider"></li>' : '',
//                        (isset(Yii::$app->user->identity->accessActions['address-type/index'])|| $isAdmin) ? ['label' => Yii::t('app', 'Address Types'), 'url' => ['/address-type']] : '',
//                        (isset(Yii::$app->user->identity->accessActions['address/index'])|| $isAdmin) ? ['label' => Yii::t('app', 'Addresses'), 'url' => ['/address']] : '',
//                        (isset(Yii::$app->user->identity->accessActions['address/index']) || $isAdmin) ? '<li class="divider"></li>' : '',
//                        (isset(Yii::$app->user->identity->accessActions['contact-type/index'])|| $isAdmin) ? ['label' => Yii::t('app', 'Contact Types'), 'url' => ['/contact-type']] : '',
//                        (isset(Yii::$app->user->identity->accessActions['contact/index'])|| $isAdmin) ? ['label' => Yii::t('app', 'Contacts'), 'url' => ['/contact']] : '',
//                        (isset(Yii::$app->user->identity->accessActions['contact/index']) || $isAdmin) ? '<li class="divider"></li>' : '',
//                        (isset(Yii::$app->user->identity->accessActions['entity-type/index'])|| $isAdmin) ? ['label' => Yii::t('app', 'Entity Types'), 'url' => ['/entity-type']] : '',
//                        (isset(Yii::$app->user->identity->accessActions['entity-class/index'])|| $isAdmin) ? ['label' => Yii::t('app', 'Entity Classes'), 'url' => ['/entity-class']] : '',
//                        (isset(Yii::$app->user->identity->accessActions['entity/index'])|| $isAdmin) ? ['label' => Yii::t('app', 'Entities'), 'url' => ['/entity']] : '',
//                        (isset(Yii::$app->user->identity->accessActions['company/index'])|| $isAdmin) ? ['label' => Yii::t('app', 'Companies'), 'url' => ['/company']] : '',
//                        (isset(Yii::$app->user->identity->accessActions['company/index']) || $isAdmin) ? '<li class="divider"></li>' : '',
//                        (isset(Yii::$app->user->identity->accessActions['person/index'])|| $isAdmin) ? ['label' => Yii::t('app', 'Persons'), 'url' => ['/person']] : '',
//                        (isset(Yii::$app->user->identity->accessActions['staff/index'])|| $isAdmin) ? ['label' => Yii::t('app', 'Staff'), 'url' => ['/staff']] : '',
//                    ],
//                ],

                // для админов
                ['label' => Yii::t('app', 'Setting'),
                    'visible' => isset(Yii::$app->user->identity->roles) && count(Yii::$app->user->identity->roles)>0 && isset(Yii::$app->user->identity) ? Yii::$app->user->identity->level >= 70 : false,
                    'items' => [
                        (isset(Yii::$app->user->identity->accessActions['user/index'])|| $isAdmin) ? ['label' => Yii::t('app', 'Users'), 'url' => ['/user']] : '',
                        (isset(Yii::$app->user->identity->accessActions['ac-role/index'])|| $isAdmin) ? ['label' => Yii::t('app', 'Ac Roles'), 'url' => ['/ac-role']] : '',
                    ],
                ],


                Yii::$app->user->isGuest ? (
                ['label' => Yii::t('app', 'Login'), 'url' => ['/site/login']]
                ) : (
                    '<li>'
                    . Html::beginForm(['/site/logout'], 'post', ['class' => 'navbar-form'])
                    . Html::submitButton(
                        Yii::t('app', 'Logout  ({user})', ['user' => Yii::$app->user->identity->username]),
                        ['class' => 'btn btn-link']
                    )
                    . Html::endForm()
                    . '</li>'
                )
            ],
        ]);

        echo Nav::widget([
            'options' => ['class' => 'navbar-nav navbar-left'],
            'items' => [

                ['label' => Yii::t('app', 'Core'),
                    'visible' => isset(Yii::$app->user->identity->roles) && count(Yii::$app->user->identity->roles)>0 && !Yii::$app->user->isGuest && (Yii::$app->user->identity->level > 0),
                    'items' => [
                        ['label' => 'ololo', 'url' => ['site/index']],
                    ],
                ],

            ],
        ]);
    } catch (Exception $e) {
        echo $e->getMessage();
    }
    NavBar::end();
    ?>


    <div class="container">

        <div class="">

            <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">

                <?php
                try {
                    echo Breadcrumbs::widget([
                        'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                    ]);
                } catch (Exception $e) {
                    echo $e->getMessage();
                } ?>

                <?= $content ?>

            </div>

        </div>

<?php
/*

        <table style="float: left; width: 100%;">
            <tr>
                <td style="vertical-align: top;">
                <?= Html::a(Html::img("@web/img/Company3.jpg", ["class"=>"img-rounded"]), Url::toRoute(['/entity-frm'])); ?><br>
                <?= Html::a(Html::img("@web/img/person2.jpg", ["class"=>"img-rounded"]), Url::toRoute(['/person'])); ?><br>
                </td>
                <td style="vertical-align: top;">
                <?= Breadcrumbs::widget([
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]) ?>


                <?= $content ?>
                </td>
            </tr>
        </table>
*/
?>

    </div>

</div>

<footer class="footer">
    <div class="container">
        <p class="pull-left">&copy; <?= date('Y') ?></p>
    </div>
</footer>

<img src="/img/ajax-loader.gif" id="loading" style="display:none; position:fixed; top:50%; left:50%" />

<?php
$js = <<<EOL
$( document ).ajaxStart(function() {
  $( "#loading" ).show();
}).ajaxStop(function() {
  $( "#loading" ).hide();
});
EOL;

$this->registerJs($js, \yii\web\View::POS_READY);

?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
