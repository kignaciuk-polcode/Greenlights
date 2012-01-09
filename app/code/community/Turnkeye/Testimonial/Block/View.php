<?php

class Turnkeye_Testimonial_Block_View extends Mage_Core_Block_Template
{
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('testimonial/testimonials.phtml');
    }
}
