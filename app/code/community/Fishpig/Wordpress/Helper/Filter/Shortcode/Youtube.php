<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Helper_Filter_Shortcode_Youtube extends Fishpig_Wordpress_Helper_Filter_Shortcode_Abstract
{
	/**
	 * Performs the filter logic
	 * Converts all youtube shortcodes into youtube videos
	 * [youtube=http://www.youtube.com/watch?v=DhtcaRRngcw]
	 *
	 * @return string
	 */
	public function applyFilter()
	{
		$parts = $this->_explode('youtube', $this->_content, true);
		$content = '';

		foreach($parts as $part) {
			$buffer = $part['content'];
			if ($part['is_opening_tag']) {
				$videoUrl = trim($this->_match("/youtube=([^\]]+)\]/", $buffer, 1), "\"' /");
				$videoCode = $this->_match("/[^a-zA-Z0-9]v=([a-zA-Z0-9-]+)/", $videoUrl, 1);
				$width = $this->_match("/[^a-zA-Z0-9]w=([0-9]+)/", $videoUrl, 1);
				$height = $this->_match("/[^a-zA-Z0-9]h=([0-9]+)/", $videoUrl, 1);
				$hideRelated = $this->_match("/[^a-zA-Z0-9](rel=0)/", $videoUrl, 1) ? true : false;
				$showSearch = $this->_match("/(showsearch=0)/", $videoUrl, 1) ? false : true;
				$autplay = $this->_match("/(autoplay=1)/", $videoUrl, 1) ? true : false;

				try {
					$part['content'] = $this->_createBlock('core/template')
							->setYoutubeVideoCode($videoCode)
							->setVideoHeight($height)
							->setVideoWidth($width)
							->setHideRelatedVideos($hideRelated)
							->setHideSearch(!$showSearch)
							->setAutoplay($autplay)
							->setTemplate('wordpress/shortcode/youtube-video.phtml')
							->toHtml();
				}
				catch (Exception $e) {
					Mage::helper('wordpress')->log('Shortcode_YouTube: '.$e->getMessage());
					$part['content'] = '';
				}
			}

			$content .= $part['content'];
		}

		return $content;
	}
	
}
