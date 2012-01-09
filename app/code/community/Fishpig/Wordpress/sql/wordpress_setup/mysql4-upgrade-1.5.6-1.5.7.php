<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

	$this->startSetup();

	$select = $this->getConnection()
		->select()
		->from($this->getTable('core/config_data'), array('config_id', 'value'))
		->where('path=?', 'wordpress_blog/filters/shortcodes');
		
	$results = $this->getConnection()->fetchAll($select);

	if (count($results) > 0) {
		foreach($results as $result) {
			$configId = $result['config_id'];
			$value = explode(',', $result['value']);

			if (($key = array_search('associatedProducts', $value)) !== false) {
				$value[$key] = 'associated_product';
				$value = implode(',', $value);
				
				$this->getConnection()->update($this->getTable('core/config_data'), array('value' => $value), $this->getConnection()->quoteInto('config_id=?', $configId));
			}		
		}
	}
	
	$this->endSetup();
	