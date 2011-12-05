<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

abstract class Fishpig_Wordpress_Helper_Filter_Abstract extends Fishpig_Wordpress_Helper_Object
{
	/**
	 * Stores the content that the filter should be applied to
	 *
	 * @var string
	 */
	protected $_content = null;
	
	/**
	 * The filter type
	 *
	 * @var string
	 */
	protected $_type = null;
	
	/**
	 * Additional parameters that will be useful for the filter
	 *
	 * @var Varien_Object
	 */
	protected $_params = null;
	
	/**
	 * Contains the filter logic
	 * All filter's must have this function and
	 * this function should ALWAYS return the content
	 *
	 * @return string
	 */
	abstract function applyFilter();
	
	
	public function __construct()
	{
		$this->_params = new Varien_Object();
		parent::__construct();
	}
	/**
	 * Sets the content variable
	 *
	 * @param string $content
	 */
	public function setContent($content)
	{
		$this->_content = $content;
		return $this;
	}
	
	/**
	 * Sets the type variable
	 *
	 * @param string $type
	 */
	public function setType($type)
	{
		$this->type = $type;
		return $this;
	}
	
	/**
	 * Sets the parameter array
	 *
	 * @param array $params
	 */
	public function setParams($params)
	{
		if ($params instanceof Varien_Object) {
			$this->_params = $params;
		}
		else {
			$this->_params->setData($params);
		}
		
		return $this;
	}
	
	/**
	 * Get the parameters
	 *
	 * @return Varien_Object
	 */
	public function getParams()
	{
		return $this->_params;
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
		if ($splitTags) {
			$pattern = "/(\[" . $shortcode . "[^\]]*\])|(\[\/".$shortcode . "\])/";
		}
		else {
			$pattern = "/(\[" . $shortcode . "[^\]]*\].*?\[\/".$shortcode . "\])/";
		}

		$parts = preg_split($pattern, $content, -1, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
		
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
}
