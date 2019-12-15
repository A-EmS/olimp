<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\AcUserRole */
/* @var $save boolean */

if (isset($model)) {
    if ($save) {
        echo '#';;
    } else {
       Yii::t('app', 'AcUserRole->add()->save() error');
    }
} else {
    echo '+';
}

    
