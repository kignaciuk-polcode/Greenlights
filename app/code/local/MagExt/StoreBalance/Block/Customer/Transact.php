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
 * Customer's Balance transactions
 *
 * @author  MagExtension Development team
 */
class MagExt_StoreBalance_Block_Customer_Transact extends Mage_Core_Block_Template 
{
	public function __construct()
	{
	    parent::__construct();
	    
	    $this->setTemplate('mgxstorebalance/customer/transact.phtml');
                
        $transactCollection = Mage::getResourceModel('mgxstorebalance/balance_transact_collection')
            ->addWebsiteFilter((int)Mage::app()->getStore()->getWebsiteId())
            ->addCustomerFilter((int)Mage::getSingleton('customer/session')->getCustomerId())
            ->setOrder('modified_date')
        ;
        $this->setTransactItems($transactCollection);
	}
	
    protected function _prepareLayout()
    {
        parent::_prepareLayout();

        $pager = $this->getLayout()->createBlock('page/html_pager', 'mgxstorebalance.balance.transact.pager')
            ->setCollection($this->getTransactItems());
        $this->setChild('pager', $pager);
        $this->getTransactItems()->load();
        return $this;
    }
    
    public function getPagerHtml()
    {
        return $this->getChildHtml('pager');
    }

    public function getActionLabel($id)
    {
        $actions = Mage::getSingleton('mgxstorebalance/balance_transact')->getActionOptions();
        if (isset($actions[$id])) {
            return $actions[$id];
        }
        return '';
    }
}