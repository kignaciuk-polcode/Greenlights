<?php

class Fishpig_Wordpress_Block_Sidebar_Widget_Tagcloud extends Fishpig_Wordpress_Block_Sidebar_Widget_Abstract
{
	/**
	 * Retrieve a collection of tags
	 *
	 * @return Fishpig_Wordpress_Model_Mysql4_Post_Tag_Collection
	 */
	public function getTags()
	{
		if (!$this->hasTags()) {
			$this->setTags(false);

			$tags = Mage::getResourceModel('wordpress/post_tag_collection')
				->addTagCloudFilter();

			$tags->load();

			if (count($tags) > 0) {
				$max = 0;
				
				foreach($tags as $tag) {
					$max = $tag->getCount() > $max ? $tag->getCount() : $max;
				}

				$this->setMaximumPopularity($max);
				$this->setTags($tags);
			}
		}

		return $this->getData('tags');
	}
	
	/**
	 * Retrieve a font size for a tag
	 *
	 * @param Fishpig_Wordpress_Model_Post_Tag $tag
	 * @return int
	 */
	public function getFontSize(Fishpig_Wordpress_Model_Post_Tag $tag)
	{
		$percentage = ($tag->getCount() * 100) / $this->getMaximumPopularity();
		
		foreach(array(25 => 90, 50 => 100, 75 => 120, 90 => 140, 100 => 150) as $percentageLimit => $default) {
			if ($percentage <= $percentageLimit) {
				return $this->_getConfigFontSize($percentage, $default);
			}
		}
		
		return $this->_getConfigFontSize(100, 150);
	}
	
	/**
	 * Retrieve a font size from the config
	 *
	 * @param string $percent
	 * @param mixed $default
	 * @return string
	 */
	protected function _getConfigFontSize($percent, $default)
	{
		$key = 'wordpress_blog/tag_cloud/font_size_below_' . $percent;
		
		return Mage::getStoreConfig($key) ? Mage::getStoreConfig($key) : $default;
	}
	
	/**
	 * Retrieve the default title
	 *
	 * @return string
	 */
	public function getDefaultTitle()
	{
		return $this->__('Blog Tags');
	}
}
