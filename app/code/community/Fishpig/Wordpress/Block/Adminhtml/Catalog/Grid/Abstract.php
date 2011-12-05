<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

abstract class Fishpig_Wordpress_Block_Adminhtml_Catalog_Grid_Abstract extends Mage_Adminhtml_Block_Widget_Grid
implements Mage_Adminhtml_Block_Widget_Tab_Interface 
{
	abstract protected function _getMagentoEntity();
	abstract protected function _getWpEntity();
	abstract protected function _getObject();

	/**
	 * Setup the grid
	 */
	public function __construct()
	{
		parent::__construct();
		$this->setId($this->_getMagentoEntity() . $this->_getWpEntity());
		$this->setDefaultSort($this->_getDefaultSort());
		$this->setDefaultDir($this->_getDefaultDir());
		$this->setSaveParametersInSession(false);
		$this->setUseAjax(true);
		
		if (is_object($this->_getObject())) {
			$this->setDefaultFilter(array($this->_getWpEntity() . '_in_' . $this->_getMagentoEntity() => 1));
		}
	}
        
	/**
	 * Returns an array of all of the product's currently associated post ID's
	 *
	 * @param string $type must be either post or category
	 * @return array
	 */
	public function getSelectedWpItems()
	{
		return array_keys($this->getSelectedWpItemPositions());
	}
	
	public function getSelectedWpItemPositions()
	{
		$postIds = array();

		if ($this->_getObject()) {
			$table = Mage::getSingleton('core/resource')
				->getTableName(sprintf('wordpress_%s_%s', $this->_getMagentoEntity(), $this->_getWpEntity()));

			$select = $this->_getReadAdapter()
				->select()
				->from($table, array($this->_getWpField(), 'position'))
				->where($this->_getMagentoField() . '=?', $this->_getObject()->getId());
				
			if ($results = $this->_getReadAdapter()->fetchAll($select)) {
				foreach($results as $data) {
					$postIds[$data[$this->_getWpField()]] = array('position_in_' . $this->_getMagentoEntity() => $data['position']);
				}
			}
		}

		return $postIds;
	}


	/**
	 * Add a custom filter for the in_product column
	 *
	 */
	protected function _addColumnFilterToCollection($column)
	{
		$primaryKey = $this->_getWpEntity() == 'post' ? 'ID' : 'term_id';

		if ($column->getId() == $this->_getWpEntity() . '_in_' . $this->_getMagentoEntity()) {
			
			$ids = $this->getSelectedWpItems();
			
			if (empty($ids)) {
				$ids = array(0);
			}
			
			if ($column->getFilter()->getValue()) {
				$this->getCollection()->addFieldToFilter("main_table." . $primaryKey, array('in' => $ids));
			}
			else {
				$this->getCollection()->addFieldToFilter("main_table." . $primaryKey, array('nin' => $ids));
			}
		}
		else {
			parent::_addColumnFilterToCollection($column);
		}
		
		return $this;
	}

	/**
	 * Displays the tab if integration is valid
	 *
	 * @return true
	 */
    public function canShowTab()
    {
		return $this->helper('wordpress')->integrationIsEnabled() && is_object($this->_getObject());
    }
    
    /**
     * Stops the tab being hidden
     *
     * @return bool
     */
    public function isHidden()
    {
    	return false;
    }
    
	/**
	 * Retrieve the class name of the tab
	 *
	 * return string
	 */
	public function getTabClass()
	{
		return 'ajax';
	}

	/**
	 * Determine whether to generate content on load or via AJAX
	 *
	 * @return bool
	 */
	public function getSkipGenerateContent()
	{
		return true;
	}
	
	protected function _getReadAdapter()
	{
		return Mage::getSingleton('core/resource')->getConnection('core_read');
	}
	
	/**
	 * Retrieve the default sort
	 *
	 * @return string
	 */
	protected function _getDefaultSort()
	{
		return 'post_date';
	}
	
	/**
	 * Retrieve the default sort direction
	 *
	 * @return string
	 */
	protected function _getDefaultDir()
	{
		return 'desc';
	}
	
	/**
	 * Retrieve the WP field used
	 *
	 * @return string
	 */
	protected function _getWpField()
	{
		return $this->_getWpEntity() . '_id';
	}
	
	/**
	 * Retrieve the Magento field
	 *
	 * @return string
	 */
	protected function _getMagentoField()
	{
		return $this->_getMagentoEntity() . '_id';
	}
	
	/**
	 * Retrieve the URL used to access the grid (AJAX)
	 *
	 * @return string
	 */
	public function getCurrentUrl($params = array())
	{
		if ($this->_getObject()) {
			return $this->getUrl('wp_admin/adminhtml_catalog_' . $this->_getMagentoEntity() . '/' . $this->_getWpEntity() . 'Grid', array('id' => $this->_getObject()->getId()));
		}
		
		return $this->getUrl('wp_admin/adminhtml_catalog_' . $this->_getMagentoEntity() . '/' . $this->_getWpEntity() . 'Grid');
	}
	
	/**
	 * Retrieve the URL used to load the tab content
	 *
	 * @return string
	 */
	public function getTabUrl()
	{
		if ($this->_getObject()) {
			return $this->getUrl('wp_admin/adminhtml_catalog_' . $this->_getMagentoEntity() . '/' . $this->_getWpEntity(), array('id' => $this->_getObject()->getId()));
		}
		
		return $this->getUrl('wp_admin/adminhtml_catalog_' . $this->_getMagentoEntity() . '/' . $this->_getWpEntity());
	}
}
