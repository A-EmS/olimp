<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

use app\models\AcFunc;
use app\models\AcRoleFunc;
use app\models\AcUserRole;

class AcAccess extends \yii\base\Model
{

    /**
     * Check access for action in 'matchCallback'
     *
     * @param yii\base\Action $action
     *
     * @return mixed
     */
    public static function checkAction($action)
    {
        if (AcUserRole::find()->where(['acur_user_id' => Yii::$app->user->identity->id, 'acur_acr_id' => 0])->exists()){
            return true;
        }

        if(in_array($action->id,['index','create','update','delete','view','export-xml',]) || $action->controller->id == 'entity-frm'){
            if ($func = AcFunc::find()->where(['acf_controller' => $action->controller->id, 'acf_action' => $action->id,])->one()) {
                if (!empty($roles = AcRoleFunc::find()->where(['acrf_acf_id' => $func->acf_id])->all())) {
                    return AcUserRole::find()->where(['acur_user_id' => Yii::$app->user->identity->id, 'acur_acr_id' => ArrayHelper::map($roles, 'acrf_acr_id', 'acrf_acr_id')])->exists();
                }
            }
            return false;
        } else {
            return true;
        }

    }

}
