<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

abstract class Fishpig_Wordpress_Helper_Filter_Shortcode_Abstract extends Fishpig_Wordpress_Helper_Filter_Abstract
{
	public function _createBlock($type, $name = null)
	{
		return Mage::getSingleton('core/layout')->createBlock($type, $name.microtime());
	}	
}
