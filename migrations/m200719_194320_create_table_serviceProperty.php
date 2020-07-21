<?php

use yii\db\Migration;

class m200719_194320_create_table_serviceProperty extends Migration
{


    public function up()
    {
        $this->execute('CREATE TABLE IF NOT EXISTS `service_property` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `name` varchar(255) NOT NULL,
              `create_user` int(11) DEFAULT \'0\',
              `create_date` datetime DEFAULT NULL,
              `update_user` int(11) DEFAULT \'0\',
              `update_date` datetime DEFAULT NULL,
              PRIMARY KEY (`id`),
              UNIQUE KEY `name_UNIQUE` (`name`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8');

        $this->execute('CREATE TABLE IF NOT EXISTS  `service_property_value` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `service_id` int(11) NOT NULL,
              `service_property_id` int(11),
              `value` varchar(255) NOT NULL,
              `create_user` int(11) DEFAULT \'0\',
              `create_date` datetime DEFAULT NULL,
              `update_user` int(11) DEFAULT \'0\',
              `update_date` datetime DEFAULT NULL,
              PRIMARY KEY (`id`),
              UNIQUE KEY `service_property` (`service_id`,`service_property_id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8');

        $this->execute('CREATE TABLE IF NOT EXISTS  `finance_documents` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `parent_document_id` int(11) NOT NULL,
              `type` int(11) NOT NULL,
              `document_number` varchar(500) NOT NULL,
              `contractor_id` int(11) NOT NULL DEFAULT 0,
              `own_company_id` int(11) NOT NULL DEFAULT 0,
              `summ` DECIMAL(10,2),
              `template` LONGBLOB,
              `signed_document_scan` varchar(1000),
              `create_user` int(11) DEFAULT \'0\',
              `create_date` datetime DEFAULT NULL,
              `update_user` int(11) DEFAULT \'0\',
              `update_date` datetime DEFAULT NULL,
              PRIMARY KEY (`id`),
              KEY `type` (`type` ASC)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8');

        $this->execute('CREATE TABLE IF NOT EXISTS  `account_content` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `account_id` int(11) NOT NULL,
              `service_id` int(11) NOT NULL,
              `service_account_price` DECIMAL(10,2),
              `service_expences_price` DECIMAL(10,2),
              `amount` int(11) NOT NULL,
              `create_user` int(11) DEFAULT \'0\',
              `create_date` datetime DEFAULT NULL,
              `update_user` int(11) DEFAULT \'0\',
              `update_date` datetime DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8');

        $this->execute('CREATE TABLE IF NOT EXISTS  `finance_book` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `account_id` int(11) NOT NULL,
              `service_id` int(11) NOT NULL,
              `service_account_price` DECIMAL(10,2),
              `service_expences_price` DECIMAL(10,2),
              `amount` int(11) NOT NULL,
              `create_user` int(11) DEFAULT \'0\',
              `create_date` datetime DEFAULT NULL,
              `update_user` int(11) DEFAULT \'0\',
              `update_date` datetime DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8');

        $this->execute('CREATE TABLE IF NOT EXISTS  `finance_operation_type` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `name` varchar(255) NOT NULL,
              `create_user` int(11) DEFAULT \'0\',
              `create_date` datetime DEFAULT NULL,
              `update_user` int(11) DEFAULT \'0\',
              `update_date` datetime DEFAULT NULL,
              PRIMARY KEY (`id`),
              UNIQUE KEY `name_UNIQUE` (`name`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8');

        $this->execute('CREATE TABLE IF NOT EXISTS  `finance_calendar` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `account_id` int(11) NOT NULL,
              `payment_status_id` int(11) NOT NULL DEFAULT 0,
              `planned_date` datetime DEFAULT NULL,
              `fact_date` datetime DEFAULT NULL,
              `create_user` int(11) DEFAULT \'0\',
              `create_date` datetime DEFAULT NULL,
              `update_user` int(11) DEFAULT \'0\',
              `update_date` datetime DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8');

        $this->execute('CREATE TABLE IF NOT EXISTS  `finance_book` (
              `id` int(11) NOT NULL AUTO_INCREMENT,
              `operation_id` int(11) NOT NULL,
              `property_1` varchar (1000),
              `property_2` varchar (1000),
              `contractor_id` datetime DEFAULT NULL,
              `operation_date` datetime DEFAULT NULL,
              `operation_type_id` int(11) NOT NULL,
              `currency_id` int(11) NOT NULL,
              `summ` DECIMAL(10,2),
              `notice` varchar (1000),
              `create_user` int(11) DEFAULT \'0\',
              `create_date` datetime DEFAULT NULL,
              `update_user` int(11) DEFAULT \'0\',
              `update_date` datetime DEFAULT NULL,
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8');

//        $this->execute('
//            INSERT INTO `access_item` (`id`, `name`, `title_alias`) VALUES (\'29\', \'servicesProperty\', \'Services Property\');
//            INSERT INTO `access_item` (`id`, `name`, `title_alias`) VALUES (\'30\', \'contracts\', \'Contracts\');
//            INSERT INTO `access_item` (`id`, `name`, `title_alias`) VALUES (\'31\', \'annexes\', \'Annexes\');
//            INSERT INTO `access_item` (`id`, `name`, `title_alias`) VALUES (\'32\', \'acts\', \'Acts\');
//            INSERT INTO `access_item` (`id`, `name`, `title_alias`) VALUES (\'33\', \'accounts\', \'Accounts\');
//            INSERT INTO `access_item` (`id`, `name`, `title_alias`) VALUES (\'34\', \'financeBook\', \'Finance Book\');
//            INSERT INTO `access_item` (`id`, `name`, `title_alias`) VALUES (\'35\', \'financeOperationType\', \'Finance Operation Type\');
//            INSERT INTO `access_item` (`id`, `name`, `title_alias`) VALUES (\'36\', \'financeCalendar\', \'Finance Calendar\');
//        ');
    }

    public function down()
    {

        $this->execute('DROP table service_property');
        $this->execute('DROP table service_property_values');
        $this->execute('DROP table finance_documents');
        $this->execute('DROP table account_content');
        $this->execute('DROP table finance_book');
        $this->execute('DROP table finance_operation_type');
        $this->execute('DROP table finance_calendar');
    }

}
