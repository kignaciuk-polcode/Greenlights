<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Helper_Filter_AutoParagraph extends Fishpig_Wordpress_Helper_Filter_Abstract
{
	/**
	 * Performs the filter logic
	 * This code has been taken from Wordpress to ensure that 
	 * the results in Magento and Wordpress match exactly!
	 *
	 * @return string
	 */
	public function applyFilter()
	{
		$content = $this->_content;
		$br = true;
		
		if ( trim($content) === '' ) {
			return $content;
		}

		$content = $content . "\n";
		$content = preg_replace('|<br />\s*<br />|', "\n\n", $content);

		$allblocks = '(?:table|thead|tfoot|caption|col|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|option|form|map|area|blockquote|address|math|style|input|p|h[1-6]|hr|fieldset|legend|section|article|aside|hgroup|header|footer|nav|figure|figcaption|details|menu|summary)';
		$content = preg_replace('!(<' . $allblocks . '[^>]*>)!', "\n$1", $content);
		$content = preg_replace('!(</' . $allblocks . '>)!', "$1\n\n", $content);
		$content = str_replace(array("\r\n", "\r"), "\n", $content);
		
		if ( strpos($content, '<object') !== false ) {
			$content = preg_replace('|\s*<param([^>]*)>\s*|', "<param$1>", $content); // no pee inside object/embed
			$content = preg_replace('|\s*</embed>\s*|', '</embed>', $content);
		}
		
		$content = preg_replace("/\n\n+/", "\n\n", $content); // take care of duplicates
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
			$content = preg_replace_callback('/<(script|style).*?<\/\\1>/s', create_function('$matches', 'return str_replace("\n", "<WPPreserveNewline />", $matches[0]);'), $content);
			$content = preg_replace('|(?<!<br />)\s*\n|', "<br />\n", $content); // optionally make line breaks
			$content = str_replace('<WPPreserveNewline />', "\n", $content);
		}
	
		$content = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*<br />!', "$1", $content);
		$content = preg_replace('!<br />(\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)[^>]*>)!', '$1', $content);
		$content = preg_replace( "|\n</p>$|", '</p>', $content );
	
		return $content;
	}
}
