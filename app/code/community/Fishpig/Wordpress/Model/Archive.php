<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Model_Archive extends Varien_Object
{
	/**
	 * Load an archive model by it's YYYY/MM
	 * EG: 2010/06
	 *
	 * @param string $value
	 */
	public function load($value)
	{
		$this->setId($value);
		
		if (strlen($value) == 7) {
			$this->setName(date('F Y', strtotime($value.'/01 01:01:01')));
		}
		else {
			$this->setName(date('F j, Y', strtotime($value.' 01:01:01')));
			$this->setIsDaily(true);
		}
		
		return $this;
	}

	/**
	 * Get the archive page URL
	 *
	 * @return string
	 */
	public function getUrl()
	{
		return Mage::helper('wordpress')->getUrl($this->getId());
	}
	
	public function hasPosts()
	{
		if ($this->hasData('post_count')) {
			return $this->getPostCount() > 0;
		}

		return $this->getPostCollection()->count() > 0;
	}
	
	public function getPostCollection()
	{
		if (!$this->hasPostCollection()) {
			$collection = Mage::getResourceModel('wordpress/post_collection')
				->addIsPublishedFilter()
				->addArchiveDateFilter($this->getId(), $this->getIsDaily())
				->setOrderByPostDate();

			$this->setPostCollection($collection);
		}
		
		return $this->getData('post_collection');
	}
}
