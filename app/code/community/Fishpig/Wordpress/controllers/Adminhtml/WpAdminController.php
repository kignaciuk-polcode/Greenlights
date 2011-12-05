<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Adminhtml_WpAdminController extends Mage_Adminhtml_Controller_Action
{
	public function loginAction()
	{
		if ($adminUser = $this->_initAdminUserModel()) {
			Mage::register('wordpress_admin_user', $adminUser);
		}
		
		$this->loadLayout();
		$this->_setActiveMenu('wordpress');
		$this->renderLayout();
	}
	
	protected function _initAdminUserModel()
	{
		$adminUser = Mage::getModel('wordpress/admin_user')->load(0, 'store_id');
			
		if ($adminUser->getId()) {
			return $adminUser;
		}
	}
	
	public function saveAction()
	{
		if ($data = $this->getRequest()->getPost()) {
			try {
				$data['user_id'] = Mage::getSingleton('admin/session')->getUser()->getUserId();
				$autologin	= Mage::getModel('wordpress/admin_user');
				$autologin->setData($data)->setId($this->getRequest()->getParam('id'));

				$autologin->save();
				Mage::getSingleton('adminhtml/session')->addSuccess($this->__('Your Wordpress Auto-login details were successfully saved.'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);				
			}
			catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
			}
		}
		else {
			Mage::getSingleton('adminhtml/session')->addError($this->__('There was an error while trying to save your Wordpress Auto-login details.'));
		}
		
        $this->_redirect('*/*/login');
	}

}
