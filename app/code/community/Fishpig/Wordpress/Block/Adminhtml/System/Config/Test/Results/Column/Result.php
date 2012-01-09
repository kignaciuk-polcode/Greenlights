<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Adminhtml_System_Config_Test_Results_Column_Result extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{
	public function _getValue(Varien_Object $row)
	{
		$data = parent::_getValue($row);
		$styles = array(
			'background-color:transparent',
			'background-position:3px 1px',
			'border:0px solid', 'display:block',
			'width:23px',
		);
		
		return '<span class="'.$data.'" style="'.implode(' !important;', $styles).'">&nbsp</span>';
	}
	
	public function getModuleName()
	{
		return parent::getModuleName() . '_Adminhtml';
	}
}