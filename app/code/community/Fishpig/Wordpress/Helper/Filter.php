<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Helper_Filter extends Fishpig_Wordpress_Helper_Abstract
{
	/**
	 * Applies a set of filters to the given string
	 *
	 * @param string $content
	 * @param array $params
	 * @return string
	 */
	public function applyFilters($content, array $params = array())
	{
		if (isset($params['object'])) {
			$this->_applyShortcodes($content, $params);
			$this->_addParagraphsToString($content);
			$this->_addMagentoFilters($content, $params);
		}
	
		return $content;
	}
	
	/**
	 * Add paragraph tags to the content
	 *
	 * @param string &$content
	 */
	protected function _addParagraphsToString(&$content)
	{
		if (trim($content) === '') {
			return;
		}
	
		$br = 1;

		$content = $content . "\n"; // just to make things a little easier, pad the end
		$content = preg_replace('|<br />\s*<br />|', "\n\n", $content);
		
		// Space things out a little
		$allblocks = '(?:table|thead|tfoot|caption|col|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|option|form|map|area|blockquote|address|math|style|input|p|h[1-6]|hr|fieldset|legend|section|article|aside|hgroup|header|footer|nav|figure|figcaption|details|menu|summary)';

		$content = preg_replace('!(<' . $allblocks . '[^>]*>)!', "\n$1", $content);
		$content = preg_replace('!(</' . $allblocks . '>)!', "$1\n\n", $content);
		$content = str_replace(array("\r\n", "\r"), "\n", $content); // cross-platform newlines
		
		if ( strpos($content, '<object') !== false ) {
			$content = preg_replace('|\s*<param([^>]*)>\s*|', "<param$1>", $content); // no pee inside object/embed
			$content = preg_replace('|\s*</embed>\s*|', '</embed>', $content);
		}

		$content = preg_replace("/\n\n+/", "\n\n", $content); // take care of duplicates
		// make paragraphs, including one at the end
		$contents = preg_split('/\n\s*\n/', $content, -1, PREG_SPLIT_NO_EMPTY);
		$content = '';
		foreach ( $contents as $tinkle ) {
			$content .= '<p>' . trim($tinkle, "\n") . "</p>\n";
		}
		
		$content = preg_replace('|<p>\s*</p>|', '', $content); // under certain strange conditions it could create a P of entirely whitespace
		$content = preg_replace('!<p>([^<]+)</(div|address|form)>!', "<p>$1</p></$2>", $content);
		$content = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $content); // don't pee all over a tag
		$content = preg_replace("|<p>(<li.+?)</p>|", "$1", $content); // problem with nested lists
		$content = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $content);
		$content = str_replace('</blockquote></p>', '</p></blockquote>', $content);
		$content = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)!', "$1", $content);
		$content = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $content);

		if ($br) {
			$content = preg_replace_callback('/<(script|style).*?<\/\\1>/s', array($this, '_preserveNewLines'), $content);
			$content = preg_replace('|(?<!<br />)\s*\n|', "<br />\n", $content); // optionally make line breaks
			$content = str_replace('<WPPreserveNewline />', "\n", $content);
		}

		$content = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*<br />!', "$1", $content);
		$content = preg_replace('!<br />(\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)[^>]*>)!', '$1', $content);
		
		if (strpos($content, '<pre') !== false) {
			$content = preg_replace_callback('!(<pre[^>]*>)(.*?)</pre>!is', array($this, '_cleanPre'), $content );
		}
		
		$content = preg_replace( "|\n</p>$|", '</p>', $content );
		
		return $content;
	}

	/**
	 * Clean PRE tags
	 * Used as callback in _addParagraphsToString
	 *
	 * @param array $matches
	 * @return string
	 */	
	public function _cleanPre($matches)
	{
		if ( is_array($matches) ) {
			$text = $matches[1] . $matches[2] . "</pre>";
		}
		else {
			$text = $matches;
		}
		
		$text = str_replace('<br />', '', $text);
		$text = str_replace('<p>', "\n", $text);
		$text = str_replace('</p>', '', $text);
		
		return $text;
	}

	/**
	 * Preserve new lines
	 * Used as callback in _addParagraphsToString
	 *
	 * @param array $matches
	 * @return string
	 */
	public function _preserveNewLines($matches)
	{
		return str_replace("\n", "<WPPreserveNewline />", $matches[0]);
	}

	/**
	 * Apply shortcodes to the content
	 *
	 * @param string &$content
	 * @param array $params = array
	 */
	protected function _applyShortcodes(&$content, $params = array())
	{
		$this->_applyCaptionShortcode($content, $params);
		$this->_applyYouTubeShortcode($content, $params);
		$this->_applyAssociatedProductsShortcode($content, $params);
		$this->_applyFeaturedProductShortcode($content, $params);
		$this->_applyGalleryShortcode($content, $params);
		
		if (Mage::getStoreConfigFlag('syntaxhighlighter/settings/is_enabled')) {
			$this->_applySyntaxHighlighterShortcode($content, $params);
		}
	}

	/**
	  * Apply the Magento filters that are applied to static blocks
	  * This allows for {{store url=""}} & {{block type="..."}} strings
	  *
	  * @param string &$content
	  * @param array $params = array()
	  */
	protected function _addMagentoFilters(&$content, $params = array())
	{
		if ($this->isLegacyMagento()) {
			$content = Mage::getModel('core/email_template_filter')->filter($content);
		}
		else {
			$content = Mage::helper('cms')->getBlockTemplateProcessor()->filter($content);
		}
	}
	
	/**
	 * Apply the caption short code
	 *
	 * @param string &$content
	 * @param array $params = array
	 */
	protected function _applyCaptionShortcode(&$content, $params = array())
	{
		$parts = $this->_explode('caption', $content);
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
					$style = ' style="width:'.($width+10).'px;"';
				}
				
				$part['content'] = "<div id=\"$id\" class=\"wp-caption $align\"$style>$innerHtml<p class=\"wp-caption-text\">$caption</div>";
			}

			$content .= $part['content'];
		}
	}
	
	/**
	 * Apply the YouTube short code
	 *
	 * @param string &$content
	 * @param array $params = array
	 */	
	protected function _applyYouTubeShortcode(&$content, $params = array())
	{
		$parts = $this->_explode('youtube', $content, true);
		$content = '';

		foreach($parts as $part) {
			if ($part['is_opening_tag']) {
				$videoUrl = trim($this->_match("/youtube=([^\]]+)\]/", $part['content'], 1), "\"' /");
				$data = array(
					'youtube_video_code' 	=> $this->_match("/[^a-zA-Z0-9]v=([a-zA-Z0-9-]+)/", $videoUrl, 1),
					'width' 							=> $this->_match("/[^a-zA-Z0-9]w=([0-9]+)/", $videoUrl, 1),
					'height' 						=> $this->_match("/[^a-zA-Z0-9]h=([0-9]+)/", $videoUrl, 1),
					'hide_related' 				=> $this->_match("/[^a-zA-Z0-9](rel=0)/", $videoUrl, 1) ? true : false,
					'show_search' 				=> $this->_match("/(showsearch=0)/", $videoUrl, 1) ? false : true,
					'autoplay'		 				=> $this->_match("/(autoplay=1)/", $videoUrl, 1) ? true : false,
					'template'						=> 'wordpress/shortcode/youtube-video.phtml',
				);
				
				$part['content'] = $this->_generateBlockTag('core/template', $data);
			}

			$content .= $part['content'];
		}
	}

	/**
	 * Apply the associated products shortcode
	 *
	 * @param string &$content
	 * @param array $params = array
	 */
	protected function _applyAssociatedProductsShortcode(&$content, $params = array())
	{
		$parts = $this->_explode('associated-products', $content, true);
		$content = '';

		foreach($parts as $part) {
			if ($part['is_opening_tag']) {
				$blockParams = array(
					'template'		=> $this->_getMatchedString($part['content'], 'template', 'wordpress/shortcode/associated-products.phtml'),
					'title'				=> $this->_getMatchedString($part['content'], 'title', Mage::helper('wordpress')->__('Featured Products')),
				);
				
				$part['content'] = $this->_generateBlockTag('wordpress/post_associated_products', $blockParams);
			}

			$content .= $part['content'];
		}
	}
	
	/**
	 * Apply the featured products shortcode
	 *
	 * @param string &$content
	 * @param array $params = array
	 */
	protected function _applyFeaturedProductShortcode(&$content, $params = array())
	{
		$parts = $this->_explode('product', $content, true);
		$content = '';

		foreach($parts as $part) {
			if ($part['is_opening_tag']) {
				$blockParams = array(
					'product_id' 		=> $this->_match("/id=['\"]([^'\"]+)['\"]/", $part['content'], 1),
					'template' 			=> $this->_getMatchedString($part['content'], 'template', 'wordpress/shortcode/product.phtml'),
				);

				if ($blockParams['product_id']) {
					$part['content'] = $this->_generateBlockTag('core/template', $blockParams);
				}
				else {
					$part['content'] = '';
				}
			}

			$content .= $part['content'];
		}
	}
	
	/**
	 * Apply the gallery shortcode
	 *
	 * @param string &$content
	 * @param array $params = array()
	 */
	protected function _applyGalleryShortcode(&$content, $params = array())
	{
		$parts = $this->_explode('gallery', $content, true);
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
					$postId = $params['object']->getId();
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
	}
	
	/**
	 * Apply the syntax highlighter shortcode
	 *
	 * @param string &$content
	 * @param array $params = array()
	 */
	protected function _applySyntaxHighlighterShortcode(&$content, $params = array())
	{
		foreach(array('code', 'sourcecode') as $tag) {
			$parts = $this->_explode($tag, $content, true);
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
		}
	}

	/**
	 * Generate a block tag for Magento to process
	 *
	 * @param string $type
	 * @param $blockparams = array()
	 * @param string $name = null
	 * @return string
	 */
	protected function _generateBlockTag($type, array $blockParams = array(), $name = null)
	{
		if (isset($blockParams['type'])) {
			unset($blockParams['type']);
		}
		
		if (!$name) {
			$name = 'wp_block_' . rand(1, 9999);
		}
		
		$blockParams['name'] 	= $name;
		$blockParams 				= array_merge(array('type' => $type), $blockParams);
		
		foreach($blockParams as $key => $value) {
			if ($value) {
				$blockParams[$key] = sprintf('%s="%s"', $key, $value);
			}
			else {
				unset($blockParams[$key]);
			}
		}	
		
		return sprintf('{{block %s}}', implode(' ', $blockParams));
	}
	
	/**
	 * Explodes a string into parts based on the given short tag
	 *
	 * @param string $shortcode
	 * @param string $content
	 * @param bool $splitTags = false
	 * @return array
	 */
	protected function _explode($shortcode, $content, $splitTags = false)
	{
		$pattern 	= $splitTags ? "/(\[" . $shortcode . "[^\]]*\])|(\[\/".$shortcode . "\])/" : "/(\[" . $shortcode . "[^\]]*\].*?\[\/".$shortcode . "\])/";
		$parts 		= preg_split($pattern, $content, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		
		return $this->_sortExplodedString($parts, $shortcode);
	}

	/**
	 * Sorts and classifies a string exploded by self::_explode
	 *
	 * @param array $parts
	 * @param string $shortcode
	 * @return array
	 */
	protected function _sortExplodedString(array $parts, $shortcode)
	{
		foreach($parts as $key => $part) {
			if (strpos($part, "[$shortcode") !== false) {
				$parts[$key] = array('is_opening_tag' => true, 'is_closing_tag' => false,  'content' => $part);
			}
			else if (strpos($part, "[/$shortcode]")  !== false) {
				$parts[$key] = array('is_opening_tag' => false, 'is_closing_tag' => true,  'content' => $part);
			}
			else {
				$parts[$key] = array('is_opening_tag' => false, 'is_closing_tag' => false, 'content' => $part);
			}
		}

		return $parts;
	}

	/**
	 * Shortcut to create a block
	 *
	 * @param string $type
	 * @param string $name = null
	 * @return Mage_Core_Block_Abstract
	 */
	public function _createBlock($type, $name = null)
	{
		return Mage::getSingleton('core/layout')->createBlock($type, $name.microtime());
	}	

	/**
	 * Returns a matched string from $buffer
	 *
	 * @param string $buffer
	 * @param string $field
	 * @return string
	 */
	protected function _getMatchedString($buffer, $field, $defaultValue = null)
	{
		return ($matchedValue = $this->_match("/".$field."=['\"]([^'\"]+)['\"]/", $buffer, 1)) ? $matchedValue : $defaultValue;
	}

	/**
	 * Wrapper for preg_match that adds extra functionality
	 *
	 * @param string $pattern
	 * @param string $value
	 * @param int $keyToReturn
	 * @return mixed
	 */
	public function _match($pattern, $value, $keyToReturn = -1)
	{
		$result = array();
		preg_match($pattern, $value, $result);
		
		if ($keyToReturn == -1) {
			return $result;
		}

		return isset($result[$keyToReturn]) ? $result[$keyToReturn] : null;
	}
}
