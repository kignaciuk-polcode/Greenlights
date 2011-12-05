<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Adminhtml_Catalog_Category_Edit_Tab_Post_Grid extends Fishpig_Wordpress_Block_Adminhtml_Catalog_Category_Edit_Tab_Grid_Abstract
{
	/**
	 * Prepare the collection of posts to display
	 *
	 */
    protected function _prepareCollection()
    {
		$collection = Mage::getResourceModel('wordpress/post_collection')
			->addIsPublishedFilter();

		$collection->getSelect()
			->columns($this->_getReadAdapter()->quoteInto('IF(ID IN (?), 1, 0) as post_in_category', $this->getSelectedWpItems()));

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }
	
	/**
	 * Prepares the columns of the grid
	 *
	 */
    protected function _prepareColumns()
    {
		$this->addColumn('post_in_category', array(
			'header_css_class'  => 'a-center',
			'type' 				=> 'checkbox',
			'name'				=> 'post_in_category',
			'align' 			=> 'center',
			'index' 			=> 'ID',
			'values' 			=> array_values($this->getSelectedWpItems()),
		));
        
		$this->addColumn('post_title', array(
			'header'=> 'Post Title',
			'index' => 'post_title',
		));
        
        $this->addColumn('post_date', array(
			'header'=> 'Post Date',
			'index' => 'post_date',
			'type' => 'date',
			'format' => 'd MMMM YYYY',
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
    	return $this->__('Associated Blog Posts');
    }
    
    /**
     * Retrieve the title used by this tab
     *
     * @return string
     */
    public function getTabTitle()
    {
    	return $this->__('Associate WordPress blog posts with this product');
    }

	/**
	 * Retrieve the name of the WP entity
	 *
	 * @return string
	 */	
	protected function _getWpEntity()
	{
		return 'post';
	}
}
