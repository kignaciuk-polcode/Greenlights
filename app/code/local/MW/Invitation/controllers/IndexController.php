<?php
class MW_Invitation_IndexController extends Mage_Core_Controller_Front_Action
{
	/**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
	public function indexAction()
    {
    	$invite = $this->getRequest()->getParam('c');
		Mage::dispatchEvent('invitation_referral_link_click',array('referral_by'=>Mage::getStoreConfig('invitation/config/referreal_by'),'invite'=>$invite,'request'=>$this->getRequest()));
		Mage::getSingleton('core/session')->addSuccess(Mage::helper('rewardpoints')->__('Thank you for visiting our site'));
		$this->_redirectUrl(Mage::getBaseUrl());
    }
}