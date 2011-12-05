<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Feed_Abstract extends Mage_Core_Block_Template
{
	public function __construct()
	{
		$this->setTemplate('wordpress/feed/default.phtml');
	}

	public function getDocType()
	{
		return '<?xml version="1.0" encoding="'.$this->getBlogCharset().'"?>'."\n";
	}
	
	public function getBlogCharset()
	{
		return Mage::helper('wordpress')->getCachedWpOption('blog_charset');
	}

	public function getRssTitle()
	{
		return $this->getBlogName();
	}

	public function getBlogName()
	{
		return $this->decode(Mage::helper('wordpress')->getCachedWpOption('blogname'));
	}
	
	public function getRssLanguage()
	{
		return Mage::helper('wordpress')->getCachedWpOption('rss_language');
	}

	public function decode($value)
	{
		return html_entity_decode($value, ENT_NOQUOTES, $this->getBlogCharset());
	}
	
	public function shorten($str, $wordCount = 100, $end = '...')
	{
		$str = strip_tags(preg_replace("/(<br[ ]{0,}>)/", "--TEST--", $str));
		$words = explode(' ', $str);
		$length = count($words);
		
		if ($length > $wordCount) {
			$words = array_splice($words, 0, $wordCount);
			return rtrim(implode(' ', $words), " .!?,;-:'\"\n") . $end;
		}
		
		return $str;
	}
	
	public function getFeaturedImageUrl(Fishpig_Wordpress_Model_Post $post)
	{
		if ($image = $post->getFeaturedImage()) {
			return $image->getThumbnailImage();
		}
		
		return false;
	}
}
