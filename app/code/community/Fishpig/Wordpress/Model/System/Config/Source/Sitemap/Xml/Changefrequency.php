<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Model_System_Config_Source_Sitemap_Xml_Changefrequency
{
	public function toOptionArray()
	{
		$buffer = array('always', 'hourly', 'daily', 'weekly', 'monthly', 'yearly', 'never');
		$options = array(array('value' => '', 'label' => Mage::helper('wordpress')->__('-- Please Select --')));
		
		foreach($buffer as $option) {
			$options[] = array(
				'value' => $option, 
				'label' => Mage::helper('wordpress')->__(ucwords($option))
			);
		}
		
		return $options;
	}
}
