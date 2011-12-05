<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Helper_Plugin_ShareThis extends Fishpig_Wordpress_Helper_Plugin_Abstract
{
	/**
	 * Determine whether the plugin has been enabled in the WordPress Admin
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		return Mage::helper('wordpress')->isPluginEnabled('Share This');
	}

	/**
	 * Determine whether to display icons on post page
	 * This is set in the ShareThis configuration in the WordPress Admin
	 *
	 * @return bool
	 */
	public function canDisplayOnPost()
	{
		return $this->isEnabled() && Mage::helper('wordpress')->getCachedWpOption('st_add_to_content') == 'yes' ? true : false;
	}
	
	/**
	 * Retrieve the Javascript include HTML
	 *
	 * @return string
	 */
	public function getJavascriptHtml()
	{
		if ($this->isEnabled() && !$this->hasJavascriptAlreadyIncluded()) {
			$this->setJavascriptAlreadyIncluded(true);
			return Mage::helper('wordpress')->getCachedWpOption('st_widget');
		}
	}
	
	/**
	 * Retrieve the icon HTML for the post
	 *
	 * @param Fishpig_Wordpress_Model_Post $post
	 * @return string
	 */
	public function getIcons(Fishpig_Wordpress_Model_Post $post)
	{
		if ($this->isEnabled()) {
			$tags = Mage::helper('wordpress')->getCachedWpOption('st_tags');
			$tags = preg_replace("/(<\?php[ ]{0,}the_title\(\)[;]{0,1}[ ]{0,}[php]{0,3}\?>)/", addslashes($post->getPostTitle()), $tags);
			$tags = preg_replace("/(<\?php[ ]{0,}the_permalink\(\)[;]{0,1}[ ]{0,}[php]{0,3}\?>)/", addslashes($post->getPermalink()), $tags);
			$tags = preg_replace("/(<\?php[ ]{0,}the_excerpt\(\)[;]{0,1}[ ]{0,}[php]{0,3}\?>)/", addslashes(strip_tags($post->getPostExcerpt())), $tags);

			if (strpos($tags, 'the_image(') !== false) {
				if ($image = $post->getFeaturedImage()) {
					$tags = preg_replace("/(<\?php[ ]{0,}the_image\(\)[;]{0,1}[ ]{0,}[php]{0,3}\?>)/", $image->getThumbnailImage(), $tags);
			
				}
			}
			return $tags;
		}
	}
}
