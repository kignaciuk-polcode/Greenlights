<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Model_System_Config_Source_Integration
{
	public function toOptionArray()
	{
		return array(
			array('value' => 0, 'label' => 'Semi Integrated'),
			array('value' => 1, 'label' => 'Fully Integrated'),
		);
	}
}
