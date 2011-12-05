<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Helper_Filter_Shortcode_Product extends Fishpig_Wordpress_Helper_Filter_Shortcode_Abstract
{
	/**
	 * Performs the filter logic
	 * Converts all product shortcodes
	 *
	 * @return string
	 */
	public function applyFilter()
	{
		$parts = $this->_explode('product', $this->_content, true);
		$content = '';

		foreach($parts as $part) {
			$buffer = $part['content'];

			if ($part['is_opening_tag']) {
				$buffer = strip_tags($buffer);
				$id = $this->_match("/id=['\"]([^'\"]+)['\"]/", $buffer, 1);
				$template = $this->_getMatchedString($buffer, 'template', 'wordpress/shortcode/product.phtml');

				if ($id) {
					$product = Mage::getModel('catalog/product')->load($id);

					if ($product->getId() > 0) {
						$part['content'] = $this->_createBlock('core/template')
							->setProduct($product)
							->setProductId($id)
							->setTemplate($template)
							->toHtml();
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
	
	/**
	 * Creates a block
	 *
	 * @param string $type
	 * @param string $name
	 * @return Mage_Core_Block_Template
	 */
	public function _createBlock($type, $name = null)
	{
		return Mage::getSingleton('core/layout')->createBlock($type, $name.microtime());
	}
}
