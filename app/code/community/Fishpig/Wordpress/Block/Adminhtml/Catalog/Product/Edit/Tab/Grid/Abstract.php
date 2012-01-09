<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

abstract class Fishpig_Wordpress_Block_Adminhtml_Catalog_Product_Edit_Tab_Grid_Abstract extends Fishpig_Wordpress_Block_Adminhtml_Catalog_Grid_Abstract
{
	/**
	 * Retrieve the name of the Magento entity
	 *
	 * @return string
	 */
	protected function _getMagentoEntity()
	{
		return 'product';
	}
	
	/**
	 * Returns the current product model
	 *
	 * @return Mage_Catalog_Model_Product
	 */
	protected function _getObject()
	{	
		return Mage::registry('product');
	}
}
