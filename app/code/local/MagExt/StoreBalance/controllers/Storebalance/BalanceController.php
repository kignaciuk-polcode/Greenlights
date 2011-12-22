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
 * Back-end Balance controller
 *
 * @author  MagExtension Development team
 */
class MagExt_StoreBalance_Storebalance_BalanceController extends MagExt_Core_Abstract_Adminhtml_Controller_Action
{
    /**
     * Check admin permissions for this controller
     *
     * @return boolean
     */
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('customer/manage');
    }
    
    /**
     * Initialize customer from request
     * @param string $idFieldName
     */
    protected function _initCustomer($idFieldName = 'id')
    {
        $customerId = (int) $this->getRequest()->getParam($idFieldName);
        $customerModel = Mage::getModel('customer/customer');
        if ($customerId)
        {
            $customerModel->load($customerId);
        }

        if (!$customerModel->getId()) {
            Mage::getSingleton('adminhtml/session')->addError($this->_helper()->__('Customer not found.'));
        }

        Mage::register('current_customer', $customerModel);
        return $this;
    }
    
    /**
     * Default action
     */
    public function indexAction()
    {
        $this->_initCustomer();
        $this->loadLayout()
            ->renderLayout();
    }
    
    /**
     * Balance transactions
     */
    public function transactGridAction()
    {
        $this->_initCustomer();
        $this->loadLayout();
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('mgxstorebalance_admin/customer_edit_tab_storebalance_transact_grid')->toHtml()
        );
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