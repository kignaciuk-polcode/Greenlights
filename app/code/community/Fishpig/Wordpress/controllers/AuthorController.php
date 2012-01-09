<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_AuthorController extends Fishpig_Wordpress_Controller_Abstract
{
	/**
	  * Initialise the current category
	  */
	protected function _init()
	{
		parent::_init();

		if ($author = $this->_initAuthor()) {
			$this->_title($author->getDisplayName())
				->_addCrumb('author_nolink', array('label' => $this->__('Author')))
				->_addCrumb('author', array('link' => $author->getUrl(), 'label' => $author->getDisplayName()))
				->_addCanonicalLink($author->getUrl());
			
			return true;
		}

		$this->throwInvalidObjectException('author');
	}

	/**
	 * Load user based on URI
	 *
	 * @return Fishpig_Wordpress_Model_User
	 */
	protected function _initAuthor()
	{
		$uri = Mage::helper('wordpress/router')->getBlogUri();
		$base = 'author';
		
		if (substr($uri, 0, strlen($base)) == $base) {
			$uri = trim(substr($uri, strlen($base)), '/');
		}

		if ($author = Mage::getModel('wordpress/user')->load($uri, 'user_nicename')) {
			if ($author->getId() > 0) {
				Mage::register('wordpress_author', $author);
				return $author;
			}
		}
		
		return false;
	}
}
