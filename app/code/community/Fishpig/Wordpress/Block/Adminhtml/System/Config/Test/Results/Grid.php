<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Adminhtml_System_Config_Test_Results_Grid extends Mage_Adminhtml_Block_Widget_Grid
{
	protected function _afterToHtml($html)
	{
		if (Mage::helper('wordpress/debug_test')->hasResults()) {
			return $html;
		}
	}
	
	/**
	 * Prepare the collection of posts to display
	 */
    protected function _prepareCollection()
    {
		$collection = $this->_getResultCollection();
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

	protected function _getResultCollection()
	{
		return Mage::helper('wordpress/debug_test')->performIntegrationTests();
	}
	
	/**
	 * Prepares the columns of the grid
	 */
    protected function _prepareColumns()
    {
		$column = $this->addColumn('result', array(
				'header'=> '&nbsp;', 
				'index' => 'result', 
				'width' => '23px'
				))->getColumn('result');
				
		$render = $this->getLayout()->createBlock('wordpress/adminhtml_system_config_test_results_column_result')->setColumn($column);
		$column->setRenderer($render);
		
		$this->addColumn('title', array(
			'header'=> 'Test Title',
			'index' => 'title',
			'width' => '260px',
		));
		
		$column = $this->addColumn('response', array(
			'header'=> 'Server Response',
			'index' => 'response',
		))->getColumn('response');

/*
		$render = $this->getLayout()->createBlock('wordpress/adminhtml_system_config_test_results_column_response')->setColumn($column);
		$column->setRenderer($render);
*/

		return parent::_prepareColumns();
	}

	
	protected function _prepareLayout()
	{
		$result = parent::_prepareLayout();
	
		$this->unsetChild('reset_filter_button');
		$this->unsetChild('search_button');
		$this->unsetChild('export_button');

		$this->_pagerVisibility = false;
		$this->_filterVisibility = false;
		
//		$this->setGridHeader('WordPress Integration Results');
	
		return $result;
	}
	
	
	public function getModuleName()
	{
		return parent::getModuleName() . '_Adminhtml';
	}
}
