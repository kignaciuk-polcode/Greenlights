<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Adminhtml_Catalog_Category_Edit_Tab_Category_Grid extends Fishpig_Wordpress_Block_Adminhtml_Catalog_Category_Edit_Tab_Grid_Abstract
{
	/**
	 * Prepare the collection of posts to display
	 *
	 */
    protected function _prepareCollection()
    {
		$collection = Mage::getResourceModel('wordpress/post_category_collection');
		$collection->getSelect()->order('name ASC');

		$collection->getSelect()
			->columns($this->_getReadAdapter()->quoteInto("IF(`main_table`.`term_id` IN (?), 1, 0) as `category_in_category`", $this->getSelectedWpItems()));

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }
	
	/**
	 * Prepares the columns of the grid
	 */
    protected function _prepareColumns()
    {
		$this->addColumn('category_in_category', array(
			'header_css_class'  => 'a-center',
			'type' 				=> 'checkbox',
			'name'				=> 'category_in_category',
			'align' 			=> 'center',
			'index' 			=> 'term_id',
			'values' 			=> array_values($this->getSelectedWpItems()),
		));
		
		$this->addColumn('name', array(
			'header'=> 'Name',
			'index' => 'name',
		));
		
		$this->addColumn('slug', array(
			'header'=> 'Slug',
			'index' => 'slug',
		));
		
		$this->addColumn('position_in_category', array(
			'header'            => Mage::helper('catalog')->__('Position'),
			'name'              => 'position_in_category',
			'type'              => 'number',
			'validate_class'    => 'validate-number',
			'index'             => 'position_in_category',
			'width'             => 100,
			'editable'          => true,
			'filterable'		=> false,
			'sortable'			=> false,
		));
		
		return parent::_prepareColumns();
	}
	
	/**
	 * Retrieve the label used for the tab relating to this block
	 *
	 * @return string
	 */
    public function getTabLabel()
    {
    	return $this->__('Associated Blog Categories');
    }
    
    /**
     * Retrieve the title used by this tab
     *
     * @return string
     */
    public function getTabTitle()
    {
    	return $this->__('Associate WordPress blog categories with this category');
    }

	/**
	 * Retrieve the name of the WP entity
	 *
	 * @return string
	 */	
	protected function _getWpEntity()
	{
		return 'category';
	}

	/**
	 * Retrieve the WP field name
	 *
	 * @return string
	 */		
	protected function _getWpField()
	{
		return 'wp_category_id';
	}
	
	/**
	 * Retrieve the default sort
	 *
	 * @return string
	 */
	protected function _getDefaultSort()
	{
		return 'name';
	}
}
