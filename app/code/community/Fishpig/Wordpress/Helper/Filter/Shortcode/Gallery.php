<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Helper_Filter_Shortcode_Gallery extends Fishpig_Wordpress_Helper_Filter_Shortcode_Abstract
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
		$parts = $this->_explode('gallery', $this->_content, true);
		$content = '';
		$galleryIt = 1;
		
		foreach($parts as $part) {
			$buffer = $part['content'];
			if ($part['is_opening_tag']) {
				$columns = trim($this->_match("/columns=[\"']{0,1}([0-9]{1,})[\"']{0,}/", $buffer, 1), "\"' /");
				
				if (!$columns) {
					$columns = 3;
				}
				
				$postId = trim($this->_match("/id=[\"']{0,1}([0-9]{1,})[\"']{0,}/", $buffer, 1), "\"' /");
				
				if (!$postId) {
					$postId = $this->getParams()->getId();
				}

				$size = trim($this->_match("/size=[\"']{0,1}([a-zA-Z]{1,})[\"']{0,1}/", $buffer, 1), "\"' /");
				
				if (!$size) {
					$size = 'thumbnail';
				}

				try {
					$post = Mage::getModel('wordpress/post')->load($postId);
					
					if ($post->getId()) {
						$images = $post->getImages();

						$part['content'] = $this->_createBlock('core/template')
							->setImageCollection($post->getImages())
							->setColumns($columns)
							->setPost($post)
							->setSize($size)
							->setGalleryIt($galleryIt++)
							->setTemplate('wordpress/shortcode/gallery.phtml')
							->toHtml();
					}
					else {
						$part['content'] = '';
					}
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
