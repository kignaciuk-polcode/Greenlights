<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Helper_Filter extends Fishpig_Wordpress_Helper_Object
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
		$filters = $this->getFilters('content');

		Mage::dispatchEvent('wordpress_content_filter_apply_before', array('content' => $content, 'params' => $params, 'filters' => $filters));

		foreach($filters as $filter) {
			$content = $this->applyFilter($content, $filter, $params);
		}

		Mage::dispatchEvent('wordpress_content_filter_apply_after', array('content' => $content, 'params' => $params, 'filters' => $filters));
		
		return $content;
	}
	
	/**
	 * Applies a specific filter to the given content string
	 *
	 * @param string $content
	 * @param string $filter
	 * @param array $params
	 * @return string $content
	 */
	public function applyFilter($content, $filter, array $params = array())
	{
		if ($helper = $this->_getHelperClass('wordpress/filter'.$this->camelize($filter))) {
			$content = $helper->setContent($content)->setType($filter)->setParams($params)->applyFilter();
		}
		
		return $content;
	}

	/**
	 * Returns the default filter set
	 *
	 * @return array
	 */
	public function getFilters($type)
	{
		return array('auto_paragraph', 'shortcodes');
	}
}
