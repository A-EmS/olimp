<?php

use app\models\AcRole;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\AcRole */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => Yii::t('app', 'Ac Role'),
]) . ' ' . $model->acr_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'acl'), 'url' => ['site/acl']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Ac Roles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->acr_id, 'url' => ['view', 'id' => $model->acr_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="ac-role-update" id="forAcRoleController">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>


    <hr />
    <h3>Доступы роли</h3>
    <button id="updateAccess" class="btn btn-info">Обновить / Сохранить таблицу доступов</button>
    <input type="hidden" value="<?php echo $model->acr_id; ?>" name="roleId" id="roleId">
    <table class="table table-striped">
        <thead>
            <tr>
                <th style="width: 300px">
                    Код
                </th>
                <th>
                    Список доступов
                    (
                    <a href="#" id="selectAllAccess" >выбрать все</a> /
                    <a href="#" id="unselectAllAccess" >снять выделение</a>
                    )
                </th>
                <th>

                </th>
                <th></th>
            </tr>
        </thead>
        <?php
        /** @var AcRole [] $acFunctions */
        /** @var AcRole $acFunction */
        $tmtArrayToStructure = [];
        foreach ($acFunctions as $acFunction){
            $nameExplodedData = explode('->', $acFunction->acf_name);
            $tmtArrayToStructure[trim($nameExplodedData[0])][] = $nameExplodedData[1]. ' <input '. ((in_array($acFunction->acf_id, $functionsInRole)) ? 'checked' : '').' class="accessCheckBox" value="'. $acFunction->acf_id .'" type="checkbox" name="access_'. $acFunction->acf_id. '" id="access_'. $acFunction->acf_id .'" style="cursor: pointer"> ';

        }

        $resultString = [];
        foreach ($tmtArrayToStructure as $structureIndex => $structureValue){
            $resultString[$structureIndex] = implode(array_map(function ($value){
                return '<div class="col-md-3" style="margin-bottom: 10px">'.$value.'</div>';
            }, $structureValue));
        }

        foreach ($resultString as $item => $value){ ?>
            <tr>
                <td>
                    <b><?php echo $item?></b>
                </td>
                <td>
                    <?php echo $value?>
                </td>
                <td>

                </td>
            </tr>
        <?php } ?>
    </table>
</div>

<script>
    $('#selectAllAccess').on('click', function(e){
        $('.accessCheckBox').prop('checked', true);
        e.stopPropagation();
        e.preventDefault();
    });

    $('#unselectAllAccess').on('click', function(e){
        $('.accessCheckBox').prop('checked', false);
        e.stopPropagation();
        e.preventDefault();
    });

    $('#updateAccess').on('click', function(e){
        var self = this;
        var roleId = $('#roleId').val();
        var idsForAccess = [];

        $('.accessCheckBox').each(function (i, v) {
            if($(v).is(':checked')){
                idsForAccess.push(parseInt($(v).val()));
            }
        });

        $.ajax({
            url: '/index.php?r=ac-role/update-access'+'&roleId='+roleId+'&idsForAccess='+idsForAccess.toString(),
            type: 'POST',
            success: function(res) {
                alert('Список доступов для роли обновлен');
            }
        });
    });
</script>