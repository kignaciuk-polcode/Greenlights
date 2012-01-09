<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Model_Observer_Adminhtml_SaveCategoryAssociations extends Fishpig_Wordpress_Model_Observer_Adminhtml_SaveAssociationsAbstract
{
	/**
	 * Save the product/post & product/category associations
	 * This information is saved in the Magento database
	 */
	public function saveAssociations(Varien_Event_Observer $observer)
	{
		try {
			if ($this->_getCategory() && $this->_getStoreId()) {
				if ($links = Mage::app()->getRequest()->getPost('links')) {
					foreach(array('post' => 'post_id', 'category' => 'wp_category_id') as $type => $wpField) {
						if (isset($links[$type . '_ids'])) {
							$this->_deleteCurrentAssociations($type);
							$this->_addNewAssociations($type, Mage::helper('adminhtml/js')->decodeGridSerializedInput($links[$type . '_ids']), $wpField);
						}
					}
				}
			}
		}
		catch (Exception $e) {
			Mage::helper('wordpress')->log($e->getMessage());
			Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
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
		$table = $this->_getResource()->getTableName('wordpress_category_'.$type);

		$this->_getResource()->getConnection('core_write')
			->delete($table, $this->_getResource()->getConnection('core_write')->quoteInto('category_id=? AND store_id=' . $this->_getStoreId(), $this->_getCategory()->getId()));
	}

	/**
	 * Adds the newassociations for the current product and the given type
	 * 
	 * @param string $type should be either post or category
	 * @return bool
	 */
	protected function _addNewAssociations($type, $assocIds, $wpField = null)
	{
		if (!$wpField) {
			$wpField = $type . '_id';
		}
		
		if (count($assocIds) > 0) {
			$categoryId = $this->_getCategory()->getId();
			$table = $this->_getResource()->getTableName('wordpress_category_'.$type);
		
			foreach($assocIds as $assocId => $data) {
				if (is_array($data)) {
					$position = array_shift($data);
				}
				else {
					$position = 0;
					$assocId = $data;
				}

				$this->_getResource()->getConnection('core_write')
					->insert($table, array('category_id' => $categoryId, $wpField => $assocId, 'position' => $position, 'store_id' => $this->_getStoreId()));
			}
		}
	}
	
	/**
	 * Loads the current Product model
	 *
	 * @return Mage_Catalog_Model_Product
	 */
	protected function _getCategory()
	{	
		return ($category = Mage::registry('category')) ? $category : false;
	}
}
