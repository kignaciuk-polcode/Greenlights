<?php
	class Turnkeye_Testimonial_Model_Mysql4_Testimonial extends Mage_Core_Model_Mysql4_Abstract
	{
		public function _construct()
		{
			$this->_init('testimonial/testimonial', 'testimonial_id');
		}
	}
