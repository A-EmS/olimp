<?php
/**
 * @author Ruslan Bondarenko (Dnipro) r.i.bondarenko@gmail.com
 * @copyright Copyright (C) 2016-2017 Ruslan Bondarenko (Dnipro)
 * @license http://www.yiiframework.com/license/
 */

namespace app\services;

use Yii;
use yii\helpers\ArrayHelper;

class PersonalService
{
    /**
     * @param int|null $individualId
     * @return false|string
     * @throws \yii\db\Exception
     */
    public function getForIndividual(int $individualId = null)
    {
        if ($individualId == null){
            $individualId = (int)Yii::$app->request->get('individual_id');
        }

        $sql = 'SELECT et.short_name as entity_type_name, e.name as entity_name, e.short_name as entity_short_name 
                FROM personal AS targetTable
                
                left join entities e ON (e.id = targetTable.entity_id)
                left join entity_types et ON (et.id = e.entity_type_id)
                where targetTable.individual_id = :individual_id
                ';

        $command = Yii::$app->db->createCommand($sql);
        $command->bindParam(":individual_id",$individualId);
        $items = $command->queryAll();

        return $items;
    }

}
