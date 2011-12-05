<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Page_ViewController extends Fishpig_Wordpress_Controller_Abstract
{
	protected function _init()
	{
		if ($page = $this->_getPage()) {
			parent::_init();
				
			if ($headBlock = $this->getLayout()->getBlock('head')) {
				if ($page->getMetaDescription()) {
					$headBlock->setDescription($page->getMetaDescription());
				}
				
				if ($page->getMetaKeywords()) {
					$headBlock->setKeywords($page->getMetaKeywords());
				}
			}

			$this->_addCanonicalLink($page->getPermalink());

			$pages = array();
			$buffer = $page;

			while ($buffer) {
				$this->_title($buffer->getPageTitle());
				$pages[] = $buffer;
				$buffer = $buffer->getParentPage();
			}
			
			$pages = array_reverse($pages);
			$lastPage = array_pop($pages);
			
			foreach($pages as $buffer) {
				$this->_addCrumb('page_' . $buffer->getId(), array('label' => $buffer->getPostTitle(), 'link' => $buffer->getPermalink()));
			}
			
			if($lastPage) {
				$this->_addCrumb('page_' . $lastPage->getId(), array('label' => $lastPage->getPostTitle()));
			}

			return true;			
		}

		return false;
	}
	
	/**
	 * Returns the current page model
	 *
	 * @return Fishpig_Wordpress_Model_Page
	 */
	protected function _getPage()
	{
		if ($page = Mage::registry('wordpress_page')) {
			if ($page->getId() && $page->getPostStatus() == 'publish') {
				return $page;
			}
		}
		
		return false;
	}
	
	/**
	 * Load layout and set custom template (page/1column.phtml) if set
	 *
	 *
	 */
    public function loadLayout($handles=null, $generateBlocks=true, $generateXml=true)
    {
    	parent::loadLayout($handles, $generateBlocks, $generateXml);    
		    	
    	if ($this->_getPage()) {
			$keys = array('onecolumn', '1column');
			$template = $this->_getPage()->getCustomField('_wp_page_template');
			
			foreach($keys as $key) {
				if (strpos($template, $key) !== false) {
					if ($rootBlock = $this->getLayout()->getBlock('root')) {
						$rootBlock->setTemplate('page/1column.phtml');
					}
					
					break;
				}
			}
    	}

    	return $this;
    }
}
