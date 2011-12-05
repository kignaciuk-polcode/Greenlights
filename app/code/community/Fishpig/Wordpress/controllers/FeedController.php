<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_FeedController extends Fishpig_Wordpress_Controller_Abstract
{
	/**
	 * Display the RSS Feed
	 *
	 */
	public function indexAction()
	{
		if (Mage::getStoreConfigFlag('wordpress_blog/rss_feed/enabled')) {
			$this->getResponse()
				->setHeader('Content-Type', 'text/xml; charset=' . Mage::helper('wordpress')->getCachedWpOption('blog_charset'), true)
				->setBody($this->getLayout()->createBlock('wordpress/feed_home')->toHtml());
		}
		else {
			$this->_forward('noRoute');
		}
	}

}
