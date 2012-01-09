<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_SitemapController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
	{
		if (Mage::getStoreConfigFlag('wordpress_blog/xml_sitemap/enabled')) {
			try {
				$sitemap = $this->getXmlSitemap();
				
				$this->getResponse()
					->setHeader('Content-Type','text/xml')
					->setBody($sitemap);
			}
			catch (Exception $e) {
				Mage::helper('wordpress')->log($e->getMessage());
				$this->_forward('noRoute');
			}
		}
		else {
			$this->_forward('noRoute');
		}
	}
	
	/**
	 * Generate the XML sitemap
	 * If the sitemap has been cached, display that instead
	 *
	 * @return string
	 */
	public function getXmlSitemap()
	{
		$sitemap = Mage::getSingleton('wordpress/sitemap_xml');
		
		$sitemap->load();
		
		if (!$sitemap->hasXml() || true) {
			$sitemap->generate();
		}
		
		return $sitemap->getXml();
	}
}
