<?php
$installer = $this;

$installer->startSetup();

$installer->run("
    -- DROP TABLE IF EXISTS {$this->getTable('social_mails')};
    CREATE TABLE {$this->getTable('social_mails')} (
        `social_mails_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
        `order_id` int(11) unsigned NOT NULL,
        `launch_date` datetime NOT NULL,
        PRIMARY KEY (`social_mails_id`),
        UNIQUE KEY `order_id` (`order_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
");

$installer->endSetup();
