<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Archive_ViewController extends Fishpig_Wordpress_Controller_Abstract
{
	/**
	  * Initialise the current category
	  */
	protected function _init()
	{
		if ($archive = $this->_initArchive()) {
			parent::_init();

			$this->_title($archive->getName())
				->_addCrumb('archive', array('link' => $archive->getUrl(), 'label' => $archive->getName()))
				->_addCanonicalLink($archive->getUrl());
			
			if ($seo = $this->getSeoPlugin()) {
				if ($seo->getPluginOption('archive_noindex')) {
					if ($headBlock = $this->getLayout()->getBlock('head')) {
						$headBlock->setRobots('noindex,follow');
					}
				}
			}

			return true;
		}

		$this->throwInvalidObjectException('archive');
	}

	/**
	 * Sets a custom root template (if set)
	 *
	 * @return Fishpig_Wordpress_Controller_Abstract
	 */
	public function setCustomRootTemplate()
	{
		if ($template = Mage::getStoreConfig('wordpress_blog/layout/template_post_list')) {
			if ($this->_setCustomRootTemplate($template)) {
				return $this;
			}
		}

		return parent::setCustomRootTemplate();
	}
	
	/**
	 * Loads an archive model based on the URI
	 *
	 * @return Fishpig_Wordpress_Model_Archive
	 */
	protected function _initArchive()
	{
		if ($archive = Mage::getModel('wordpress/archive')->load(Mage::helper('wordpress/router')->getBlogUri())) {
			if ($archive->hasPosts()) {
				Mage::register('wordpress_archive', $archive);
				return $archive;
			}
		}

		return false;
	}
}
