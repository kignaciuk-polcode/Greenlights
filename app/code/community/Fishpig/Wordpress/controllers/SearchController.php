<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_SearchController extends Fishpig_Wordpress_Controller_Abstract
{
	/**
	  * Initialise the current category
	  */
	protected function _init()
	{
		parent::_init();

		$helper = Mage::helper('wordpress/search');
		$routerHelper = $this->getRouterHelper();
		
		if ($searchValue = $routerHelper->getTrimmedUri('search')) {
			$this->getRequest()->setParam($helper->getQueryVarName(), $searchValue);
		}

		$label = $this->__("Search results for: '%s'", $helper->getEscapedSearchString());

		$this->_title($label);
		$this->_addCrumb('blog_search', array('link' => '', 'label' => $label));

		return true;
	}
}
