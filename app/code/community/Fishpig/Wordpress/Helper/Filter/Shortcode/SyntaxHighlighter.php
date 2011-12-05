<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Helper_Filter_Shortcode_SyntaxHighlighter extends Fishpig_Wordpress_Helper_Filter_Shortcode_Abstract
{
	/**
	 * Performs the filter logic
	 *
	 * @return string
	 */
	public function applyFilter()
	{
		$parts = $this->_explode('sourcecode', $this->_content, true);
		$content = '';
		$nextIsSource = false;
		$language = '';
		
		foreach($parts as $part) {
			$buffer = $part['content'];

			if ($part['is_opening_tag']) {
				$nextIsSource = true;
				$language = $this->_getMatchedString($buffer, 'language');
				$part['content'] = '';
			}
			elseif ($nextIsSource) {
				$nextIsSource = false;
				$part['content'] = '<pre class="brush: ' . ($language ? $language : 'php') . '">' . htmlentities($part['content']) . '</pre>';
			}
			elseif ($part['is_closing_tag']) {
				$part['content'] = '';
			}

			$content .= $part['content'];
		}

		return $content;
	}
    
    /**
     * Determine wether the Syntax Highlighter is enabled and installed
     *
     * @return bool
     */
    public function isEnabled()
    {
    	return Mage::getStoreConfigFlag('cms/syntaxhighlighter/is_enabled');
    }
}
