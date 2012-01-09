<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Sidebar extends Mage_Core_Block_Template
{
	/**
	 * Stores all templates for each widget block
	 *
	 * @var array
	 */
	protected $_widgets = array();

	/**
	 * Add a widget type
	 *
	 * @param string $name
	 * @param string $block
	 * @param string $template
	 * @return Fishpig_Wordpress_Block_Sidebar
	 */
	public function addWidgetType($name, $block, $template)
	{
		$this->_widgets[$name] = array(
			'block' => strpos($block, '/') !== false ? $block : 'wordpress/' . $block,
			'template' => $template
		);
		
		return $this;
	}
	
	/**
	 * Retrieve information about a widget type
	 *
	 * @param string $name
	 * @return false|array
	 */
	public function getWidgetType($name)
	{
		return isset($this->_widgets[$name]) ? $this->_widgets[$name] : false;
	}
	
	/**
	 * Load all enabled widgets
	 *
	 * @return Fishpig_Wordpress_Block_Sidebar
	 */
	protected function _beforeToHtml()
	{
		if ($widgets = $this->getWidgetsArray()) {
			foreach($widgets as $widgetType) {

				$name = $this->_getWidgetName($widgetType);
				$widgetIndex = $this->_getWidgetIndex($widgetType);

				if ($widget = $this->getWidgetType($name)) {
					if ($block = $this->getLayout()->createBlock($widget['block'])) {
						$block->setTemplate($widget['template']);
						$block->setWidgetType($name);
						$block->setWidgetIndex($widgetIndex);
						
						$this->setChild('wordpress_widget_' . $widgetType, $block);
					}
				}
			}
		}

		return parent::_beforeToHtml();
	}
	
	/**
	 * Retrieve the widget name
	 * Strip the trailing number and hyphen
	 *
	 * @param string $widget
	 * @return string
	 */
	protected function _getWidgetName($widget)
	{
		return rtrim(preg_replace("/[^a-z_-]/i", '', $widget), '-');
	}
	
	/**
	 * Retrieve the widget name
	 * Strip the trailing number and hyphen
	 *
	 * @param string $widget
	 * @return string
	 */
	protected function _getWidgetIndex($widget)
	{
		if (preg_match("/([0-9]{1,})/",$widget, $results)) {
			return $results[1];
		}
		
		return false;
	}
	
	/**
	 * Retrieve the sidebar widgets as an array
	 *
	 * @return false|array
	 */
	public function getWidgetsArray()
	{
		if ($this->getWidgetArea()) {
			$widgets = $this->helper('wordpress')->getCachedWpOption('sidebars_widgets');

			if ($widgets) {
				$widgets = unserialize($widgets);
				
				if (isset($widgets[$this->getWidgetArea()])) {
					return $widgets[$this->getWidgetArea()];
				}
				
				if (isset($widgets[$this->getWidgetFallbackArea()])) {
					return $widgets[$this->getWidgetFallbackArea()];
				}
			}
		}

		return false;
	}
}
