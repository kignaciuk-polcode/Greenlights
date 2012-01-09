<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Model_Link extends Mage_Core_Model_Abstract
{
	public function _construct()
	{
		$this->_init('wordpress/link');
	}
}
