<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Sidebar_Widget_Abstract extends Mage_Core_Block_Template
{
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

		return parent::_beforeToHtml();
	}
}
