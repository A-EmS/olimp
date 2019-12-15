<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\AcRoleFunc */
/* @var $save boolean */

if (isset($model)) {
    if ($save) {
        echo '#'; //$model->acrf_id;
    } else {
       Yii::t('app', 'AcRoleFunc->add()->save() error');
    }
} else {
    echo '+';
}

    
