<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Model_System_Config_Source_Database_Location
{
	public function toOptionArray()
	{
		return array(
			array('value' => 0, 'label' => 'Magento/WordPress share a database'),
			array('value' => 1, 'label' => 'Magento/WordPress do not share a database'),
		);
	}
}
