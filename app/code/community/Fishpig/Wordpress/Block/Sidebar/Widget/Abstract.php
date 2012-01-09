<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

abstract class Fishpig_Wordpress_Block_Sidebar_Widget_Abstract extends Mage_Core_Block_Template
{
	abstract public function getDefaultTitle();
	
	protected function _construct()
	{
        $this->addData(array(
            'cache_lifetime'    => 120,
            'cache_tags'        => array('wordpress_sidebar_widget'),
        ));
        
        return parent::_construct();
	}
	
	/**
	 * Retrieve the default title
	 *
	 * @return string
	 */
	public function getTitle()
	{
		return $this->_getData('title') ? $this->_getData('title') : $this->getDefaultTitle();
	}
	
	/**
	 * Attempt to load the widget information from the WordPress options table
	 *
	 * @return Fishpig_Wordpress_Block_Sidebar_Widget_Abstract
	 */
	protected function _beforeToHtml()
	{
		if ($this->getWidgetType()) {
			$data = $this->helper('wordpress')->getCachedWpOption('widget_' . $this->getWidgetType());

			if ($data) {
				$data = unserialize($data);
				
				if (isset($data[$this->getWidgetIndex()])) {
					foreach($data[$this->getWidgetIndex()] as $field => $value) {
						$this->setData($field, $value);
					}
				}
			}
		}

		$this->setCacheKey($this->getNameInLayout());

		return parent::_beforeToHtml();
	}
}
