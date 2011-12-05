<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Model_Observer_Adminhtml_SaveProductAssociations
{
	/**
	 * Save the product/post & product/category associations
	 * This information is saved in the Magento database
	 */
	public function saveAssociations(Varien_Event_Observer $observer)
	{
		try {
			if ($this->_getProduct()) {
				if ($links = Mage::app()->getRequest()->getPost('links')) {
					foreach(array('post', 'category') as $type) {
						if (isset($links[$type . '_ids'])) {
							$this->_deleteCurrentAssociations($type);
							$this->_addNewAssociations($type, Mage::helper('adminhtml/js')->decodeGridSerializedInput($links[$type . '_ids']));
						}
					}
				}
			}
		}
		catch (Exception $e) {
			Mage::helper('wordpress')->log($e->getMessage());
		}
	}

	/**
	 * Deletes the associations for the current product and the given type
	 * 
	 * @param string $type should be either post or category
	 * @return bool
	 */
	protected function _deleteCurrentAssociations($type)
	{
		$table = $this->_getResource()->getTableName('wordpress_product_'.$type);

		$this->_getResource()->getConnection('core_write')
			->delete($table, $this->_getResource()->getConnection('core_write')->quoteInto('product_id=?', $this->_getProduct()->getId()));
	}

	/**
	 * Adds the newassociations for the current product and the given type
	 * 
	 * @param string $type should be either post or category
	 * @return bool
	 */
	protected function _addNewAssociations($type, $assocIds)
	{
		if (count($assocIds) > 0) {
			$productId = $this->_getProduct()->getId();
			$table = $this->_getResource()->getTableName('wordpress_product_'.$type);
		
			foreach($assocIds as $assocId => $data) {
				if (is_array($data)) {
					$position = array_shift($data);
				}
				else {
					$position = 0;
					$assocId = $data;
				}

				$this->_getResource()->getConnection('core_write')
					->insert($table, array('product_id' => $productId, "{$type}_id" => $assocId, 'position' => $position));
			}
		}
	}
	
	/**
	 * Loads the current Product model
	 *
	 * @return Mage_Catalog_Model_Product
	 */
	protected function _getProduct()
	{	
		return ($product = Mage::registry('product')) ? $product : false;
	}
	
	/**
	 * Retrieve the resource class
	 *
	 */
	protected function _getResource()
	{
		return Mage::getSingleton('core/resource');
	}
}
