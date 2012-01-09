<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Model_System_Config_Source_Web_Redirect
{
	/**
	 * Retrieve an array of shortcodes
	 *
	 * @return array
	 */
	public function toOptionArray()
	{ 
        return array(
            array('value' => 0, 'label'=>Mage::helper('adminhtml')->__('No')),
            array('value' => 302, 'label'=>Mage::helper('adminhtml')->__('Yes (302 Found)')),
            array('value' => 301, 'label'=>Mage::helper('adminhtml')->__('Yes (301 Moved Permanently)')),
        );
	}
}
