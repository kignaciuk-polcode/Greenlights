<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Adminhtml_SitemapController extends Mage_Adminhtml_Controller_Action
{
	/**
	 * Generate a WordPress XML sitemap
	 *
	 */
	public function generateAction()
	{
		try {
			$sitemap = Mage::getSingleton('wordpress/sitemap_xml')->generate();
			$sitemap->save();
			
			$this->_getSession()->addSuccess($this->__('Your XML sitemap has been generated'));
		}
		catch (Exception $e) {
			Mage::helper('wordpress')->log($e->getMessage());
			$this->_getSession()->addError($e->getMessage());
		}
		
		$this->_redirectReferer();
	}
}
