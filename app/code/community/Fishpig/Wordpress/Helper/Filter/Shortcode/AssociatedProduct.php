<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Helper_Filter_Shortcode_AssociatedProduct extends Fishpig_Wordpress_Helper_Filter_Shortcode_Abstract
{
	/**
	 * Performs the filter logic
	 *
	 * @return string
	 */
	public function applyFilter()
	{
		$parts = $this->_explode('associated-products', $this->_content, true);
		$content = '';

		foreach($parts as $part) {
			$buffer = $part['content'];

			if ($part['is_opening_tag']) {
				$postId = $this->getParams()->getId();
				$template = $this->_getMatchedString($buffer, 'template', 'wordpress/shortcode/associated-products.phtml');
				$title = $this->_getMatchedString($buffer, 'title', Mage::helper('wordpress')->__('Associated Products'));
				
				if ($postId) {
					$post = Mage::getModel('wordpress/post')->load($postId);
					
					if ($post->getId() > 0) {
						try {
							$part['content'] = $this->_createBlock('wordpress/post_associated_products')
									->setPostId($postId)
									->setTitle($title)
									->setTemplate($template)
									->toHtml();
						}
						catch (Exception $e) {
							Mage::helper('wordpress')->log('Shortcode_AssociatedProducts: '.$e->getMessage());
							$part['content'] = '';
						}
					}
				}
				else {
					Mage::helper('wordpress')->log('Invalid product shortcode: '.htmlspecialchars($part['content']));
				}
			}

			$content .= $part['content'];
		}

		return $content;
	}
}
