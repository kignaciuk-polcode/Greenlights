<?php
    $installer = $this;
    $installer->startSetup();
    $table = $this->getTable('testimonials');

    $this->startSetup()->run("
		drop table if exists {$table};
		create table {$table} (
			testimonial_id int(11) unsigned not null auto_increment,
			testimonial_position int(11) default 0,
			testimonial_name varchar(50) not null default '',
			testimonial_text text not null default '',
			testimonial_img varchar(128) default NULL,
			testimonial_sidebar tinyint(4) NOT NULL,
			PRIMARY KEY(testimonial_id)
		) engine=InnoDB default charset=utf8;
	")->endSetup();