<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Helper_Filter_Shortcodes extends Fishpig_Wordpress_Helper_Filter_Abstract
{
	/**
	 * Returns an array containing all currently supported shortcodes
	 *
	 * @return array
	 */
	public function getShortcodes()
	{
		$shortcodes = array(
			'caption',
			'youtube',
			'product',
			'associated_product',
			'gallery',
		);
		
		if ($this->_getShortcodeHelperClass('syntaxHighlighter')->isEnabled()) {
			$shortcodes[] = 'syntaxHighlighter';
		}

		return $shortcodes;
	}
	
	/**
	 * Performs the filter logic
	 *
	 * @return string
	 */
	public function applyFilter()
	{
		$content = trim($this->_content);
		$shortcodes = $this->getShortcodes();
		
		if ($content) {
			foreach($shortcodes as $shortcode) {
				if ($helper = $this->_getShortcodeHelperClass($shortcode)) {
					$content = $helper->setContent($content)->setType($shortcode)->setParams($this->_params)->applyFilter();
				}
			}
		}
		
		return $content;
	}

	/**
	 * Returns a helper based on the shortcode given
	 *
	 * @param string $shortcode
	 * @return Fishpig_Wordpress_Helper_Filter_Shortcode_Abstract
	 */
	protected function _getShortcodeHelperClass($shortcode)
	{
		return $this->_getHelperClass('wordpress/filter_shortcode'.$this->camelize($shortcode));
	}
	
}
