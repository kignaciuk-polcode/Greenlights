<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Helper_Filter_Shortcode_Caption extends Fishpig_Wordpress_Helper_Filter_Shortcode_Abstract
{
	/**
	 * Performs the filter logic
	 * Converts all caption shortcodes into div tags
	 *
	 * @return string
	 */
	public function applyFilter()
	{
		$parts = $this->_explode('caption', $this->_content);
		$content = '';

		foreach($parts as $part) {
			$buffer = $part['content'];

			if ($part['is_opening_tag']) {
				$id = $this->_match("/id=['\"]([^'\"]+)['\"]/", $buffer, 1);
				$align = str_replace("align", "", $this->_match("/align=['\"](align[^'\"]+)['\"]/", $buffer, 1));
				$width = $this->_match("/width=['\"]([0-9]+)['\"]/", $buffer, 1);
				$caption = $this->_match("/caption=['\"]([^'\"]+)['\"]/", $buffer, 1);
				$innerHtml = $this->_match("/\[caption[^\]]*\](.*?)\[\/caption\]/", $buffer, 1);
				$style = '';
				
				if ($align != 'center') {
					$style = ' style="width:'.($width+10).'px;';
				}
				
				$part['content'] = "<div id=\"$id\" class=\"wp-caption $align\"$style>$innerHtml<p class=\"wp-caption-text\">$caption</div>";
			}

			$content .= $part['content'];
		}

		return $content;
	}
	
}
