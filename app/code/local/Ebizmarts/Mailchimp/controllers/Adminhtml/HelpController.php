<?php

class Ebizmarts_Mailchimp_Adminhtml_HelpController extends Mage_Adminhtml_Controller_Action{

	public function indexAction(){

        $this->loadLayout()
        	->_setActiveMenu('newsletter')
        	->_addContent($this->getLayout()->createBlock('mailchimp/adminhtml_help'))
        	->renderLayout();
    }

}