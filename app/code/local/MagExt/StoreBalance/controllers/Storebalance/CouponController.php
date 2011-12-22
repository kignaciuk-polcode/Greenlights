<?php
/**
 * MagExtension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MagExtension EULA 
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magextension.com/LICENSE.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@magextension.com so we can send you a copy.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to http://www.magextension.com for more information.
 *
 * @category   MagExt
 * @package    MagExt_StoreBalance
 * @copyright  Copyright (c) 2010 MagExtension (http://www.magextension.com/)
 * @license    http://www.magextension.com/LICENSE.txt End-User License Agreement
 */
 
/**
 * Back-end Coupon controller
 *
 * @author  MagExtension Development team
 */
class MagExt_StoreBalance_Storebalance_CouponController extends MagExt_Core_Abstract_Adminhtml_Controller_Action
{
    /**
     * Check admin permissions for this controller
     *
     * @return boolean
     */
    protected  function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('promo/storebalancecoupon'); 
    }
    
	/**
	 * Action initialization 
	 */
	protected function _initAction()
	{
		$this->loadLayout();
		$this->_setActiveMenu('promo');
	}
	
	/**
	 * Default Action
	 */
	public function indexAction()
	{
	    $block = $this->getLayout()->createBlock('mgxstorebalance_admin/coupon');
		$this->_initAction();
		$this->_addContent($block)
		  ->renderLayout();
	}
	
	/**
	 * Coupon Grid Action
	 */
	public function gridAction()
	{
	    $this->getResponse()->setBody(
            $this->getLayout()->createBlock('mgxstorebalance_admin/coupon_grid', 'mgxstorebalance.coupon.grid')
                ->toHtml()
        );
	}
	
	/**
	 * @see self::editAction()
	 */
	public function newAction()
	{
	    $this->_forward('edit');
	}
	
	/**
	 * Edit Store Balance Coupon Action
	 */
	public function editAction()
    {
        try {
        	$coupon = $this->_initCoupon();
            $data = Mage::getSingleton('adminhtml/session')->getFormData(true);
            if (!empty($data)) {
                if (!empty($data['settings']))
                    $coupon->addData($data['settings']);
                if (!empty($data['details']))
                    $coupon->addData($data['details']);
                if (!empty($data['use_config']))
                {
                    foreach ($data['use_config'] as $config => $value)
                    {
                        $coupon->setData($config.'_use_config', $value);
                    }
                }
            }
            $block = $this->getLayout()->createBlock('mgxstorebalance_admin/coupon_edit');
            
            $this->_initAction();
            $this->_addContent($block)
                ->_addLeft($this->getLayout()->createBlock('mgxstorebalance_admin/coupon_edit_tabs'))
                ->renderLayout();
        }
        catch (Exception $e)
        {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_redirect('*/*');
            return false;
        }
    }
    
    /**
     * Save Store Balance Coupon Action
     */
    public function saveAction()
    {
        if ($data = $this->getRequest()->getPost()) {
            try {
                $dataDetails = $this->getRequest()->getPost('details');
                $dataDetails = $this->_filterDates($dataDetails, array('from_date', 'to_date'));
                $data['details'] = $dataDetails;
                
                $couponModel = $this->_initCoupon();
                $validateResult = $couponModel->validateData(new Varien_Object($data['details']));
                if ($validateResult !== true) {
                    foreach($validateResult as $errorMessage) {
                        $this->_getSession()->addError($errorMessage);
                    }
                    $this->_getSession()->setFormData($data);
                    $this->_redirect('*/*/edit', array('id'=>$couponModel->getId()));
                    return;
                }
                $couponModel->loadPost($data);
                if ($couponModel->getIsNew())
                {
                    $couponModel->generate();
                    $successMessage = $this->_helper()->__('%d Store Balance Coupon(s) generated', $couponModel->getData('qty'));
                }
                else 
                {
                    $couponModel->save();
                    $successMessage = $this->_helper()->__('Store Balance Coupon saved');
                }

                $this->_getSession()->addSuccess($successMessage);
                $this->_getSession()->setFormData(false);
                $this->_redirect('*/*/');
                return true;
            }
            catch (Exception $e)
            {
                $this->_getSession()->addError($e->getMessage());
                $this->_getSession()->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return false;
            }
        }
    }
    
    /**
     * Store Balance Coupon history
     */
    public function historyGridAction()
    {
        try {
            $coupon = $this->_initCoupon(false);
            $this->_initAction();
            $this->getResponse()->setBody(
                $this->getLayout()->createBlock('mgxstorebalance_admin/coupon_edit_tab_history')->toHtml()
            );
        }
        catch (Exception $e)
        {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            $this->_redirect('*/*');
            return false;
        }
    }
    
    /**
     * Delete coupon
     */
    public function deleteAction()
    {
        try {
            $coupon = $this->_initCoupon(false);
            if ($coupon->isDeletable()) {
                $coupon->delete();
                Mage::getSingleton('adminhtml/session')->addSuccess($this->_helper()->__('Coupon deleted'));
                $this->_redirect('*/*/');
                return;
            }
            else {
                Mage::throwException($this->_helper()->__("Store Balance Coupon can't be deleted."));
            }
        }
        catch (Exception $e)
        {
            Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            if ($id = $coupon->getId()) {
                $this->_redirect('*/*/edit', array('id' => $id));
            }
            else {
                $this->_redirect('*/*');
            }
            return false;
        }
    }
    
    /**
     * Mass delete coupons
     */
    public function massDeleteAction()
    {
        $couponIds = $this->getRequest()->getParam('coupon');
        if (!is_array($couponIds)) {
            $this->_getSession()->addError($this->__('Please select coupon(s)'));
        }
        else {
            try {
                foreach ($couponIds as $couponId) {
                    $coupon = Mage::getSingleton('mgxstorebalance/coupon')->load($couponId);
                    Mage::dispatchEvent('coupon_controller_coupon_delete', array('coupon' => $coupon));
                    $coupon->delete();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully deleted', count($couponIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
    
    /**
     * Mass update of coupons' statuses
     */
    public function massStatusAction()
    {
        $couponIds = (array)$this->getRequest()->getParam('coupon');
        $status     = (int)$this->getRequest()->getParam('status');

        $couponModel = Mage::getModel('mgxstorebalance/coupon');

        try {
            foreach ($couponIds as $couponId) {
                $couponModel->updateCouponStatus($couponId, $status);
            }
            $this->_getSession()->addSuccess(
                $this->__('Total of %d record(s) were successfully updated', count($couponIds))
            );
        }
        catch (Mage_Core_Model_Exception $e) {
            $this->_getSession()->addError($e->getMessage());
        }
        catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('There was an error while updating coupon(s) status'));
        }

        $this->_redirect('*/*/index');
    }
    
    /**
     * Initialize coupon from request parameters
     *
     * @return MagExt_StoreBalance_Model_Coupon
     */
    protected function _initCoupon($bInitNew = true)
    {
    	$couponId    = (int) $this->getRequest()->getParam('id');
        $couponModel = Mage::getModel('mgxstorebalance/coupon');
        $bWrongCoupon = false;
        if (!$couponId && $bInitNew)
        {
            $couponModel->setIsNew(true);
        }
        elseif ($couponId)
        {
            $couponModel->load($couponId);
            if ($couponModel->getId() != $couponId)
            {
                $bWrongCoupon = true;
            }
            
        }
        else // $bInitNew == false
        {
            $bWrongCoupon = true;
        }
        if ($bWrongCoupon)
        {
            Mage::throwException($this->_helper()->__('Wrong Store Balance Coupon specified.'));
        }
        Mage::register('current_storebalance_coupon', $couponModel);
        return $couponModel;
    }

    /**
     * Return helper
     * @return MagExt_StoreBalance_Helper_Data
     */
    protected function _helper()
    {
    	return Mage::helper('mgxstorebalance');
    }
}