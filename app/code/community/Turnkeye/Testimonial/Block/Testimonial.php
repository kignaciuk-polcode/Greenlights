<?php
class Turnkeye_Testimonial_Block_Testimonial extends Mage_Core_Block_Template
{
	public function _prepareLayout()
	{
		return parent::_prepareLayout();
	}

	public function getTestimonials()
	{
		if (!$this->hasData('testimonial')) {
			$this->setData('testimonial', Mage::registry('testimonial'));
		}
		return $this->getData('testimonial');
	}

}
