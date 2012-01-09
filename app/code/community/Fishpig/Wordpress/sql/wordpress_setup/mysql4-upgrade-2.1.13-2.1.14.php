<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

	$this->startSetup();

	/**
	 * Update the table primary key to include the store_id field
	 * This allows for multi-store associations
	 *
	 */
	$this->run("
		ALTER TABLE {$this->getTable('wordpress_product_post')} DROP PRIMARY KEY;
		ALTER TABLE {$this->getTable('wordpress_product_post')} ADD PRIMARY KEY (`product_id`, `post_id`, `store_id`);
		
		ALTER TABLE {$this->getTable('wordpress_product_category')} DROP PRIMARY KEY;
		ALTER TABLE {$this->getTable('wordpress_product_category')} ADD PRIMARY KEY (`product_id`, `category_id`, `store_id`);
		
		ALTER TABLE {$this->getTable('wordpress_category_post')} DROP PRIMARY KEY;
		ALTER TABLE {$this->getTable('wordpress_category_post')} ADD PRIMARY KEY (`category_id`, `post_id`, `store_id`);
		
		ALTER TABLE {$this->getTable('wordpress_category_category')} DROP PRIMARY KEY;
		ALTER TABLE {$this->getTable('wordpress_category_category')} ADD PRIMARY KEY (`category_id`, `wp_category_id`, `store_id`);
	");

	$this->endSetup();
