<?php
/**
 * @author Ruslan Bondarenko (Dnipro) r.i.bondarenko@gmail.com
 * @copyright Copyright (C) 2016-2017 Ruslan Bondarenko (Dnipro)
 * @license http://www.yiiframework.com/license/
 */

namespace app\services;

use Yii;
use yii\helpers\ArrayHelper;

class HeadSearchBoxService
{
    public function searchInContractors(int $id)
    {
        $itemsToPrint = [];

        $entityCheckSql = 'SELECT c.is_entity, targetTable.contractor_id
                FROM contacts AS targetTable
                left join contractor c ON (c.id = targetTable.contractor_id)
                where targetTable.id = :id
                ';

        $command = Yii::$app->db->createCommand($entityCheckSql);
        $command->bindParam(":id",$id);
        $result = $command->queryOne();

        $isEntity = (bool)$result['is_entity'];
        $contractorId = $result['contractor_id'];

        $contactsSql = 'SELECT targetTable.*, ct.contact_type
                FROM contacts AS targetTable
                left join contact_types ct ON (ct.id = targetTable.contact_type_id)
                where targetTable.contractor_id = :contractor_id
                ';

        $command = Yii::$app->db->createCommand($contactsSql);
        $command->bindParam(":contractor_id",$contractorId);
        $contactsItems = $command->queryAll();
        $contactsToPrint = [];
        foreach ($contactsItems as $contactsItem) {
            $contactsToPrint[ucfirst($contactsItem['contact_type'])] = $contactsItem['name'];
        }

        if ($isEntity === true) {
            $sql = 'SELECT ctr.full_name as country_full_name, et.short_name as ownership, e.name, e.short_name, e.ogrn, e.inn, e.notice
                FROM contacts AS targetTable
                left join contractor c ON (c.id = targetTable.contractor_id)
                left join entities e ON (e.id = c.ref_id and c.is_entity = 1)
                left join entity_types et ON (et.id = e.entity_type_id)
                left join countries ctr ON (ctr.id = targetTable.country_id)
                where targetTable.id = :id
                ';

            $command = Yii::$app->db->createCommand($sql);
            $command->bindParam(":id",$id);
            $entity = $command->queryOne();

            $itemsToPrint = [
                'Country' => $entity['country_full_name'],
                'Type Of Ownership' => $entity['ownership'],
                'Entity Name' => $entity['name'],
                'Short Entity Name' => $entity['short_name'],
                'OGRN' => $entity['ogrn'],
                'INN' => $entity['inn'],
                'Notice' => $entity['notice'],
            ];

            $itemsToPrint = array_merge($itemsToPrint, $contactsToPrint);
        } else {
            $personalService = new PersonalService();

            $sql = 'SELECT i.id, i.third_name, i.name, i.second_name, i.notice
                FROM contacts AS targetTable
                left join contractor c ON (c.id = targetTable.contractor_id)
                left join individuals i ON (i.id = c.ref_id and c.is_entity = 0)
                where targetTable.id = :id
                ';

            $command = Yii::$app->db->createCommand($sql);
            $command->bindParam(":id",$id);
            $individual = $command->queryOne();

            $itemsToPrint = [
                'Third Name' => $individual['third_name'],
                'Name' => $individual['name'],
                'Second Name' => $individual['second_name'],
                'Notice' => $individual['notice'],
            ];

            $itemsToPrint = array_merge($itemsToPrint, $contactsToPrint);

            foreach ($personalService->getForIndividual($individual['id']) as $entityForIndividual) {
                $itemsToPrint['Entities For Individuals'][] = $entityForIndividual;
            }
        }

        return $itemsToPrint;
    }

}
