<?php
class Devinc_Multipledeals_IndexController extends Mage_Core_Controller_Front_Action
{	 
    public function indexAction()
    {
		Mage::getModel('multipledeals/multipledeals')->refreshDeals();
        $product_id = Mage::getModel('multipledeals/multipledeals')->getCollection()->addFieldToFilter('type', array('eq'=>'1'))->getFirstItem()->getProductId();
		
        if ($product_id!=0 && Mage::getStoreConfig('multipledeals/configuration/enabled')) {
            $_product = Mage::getModel('catalog/product')->load($product_id);				
			header('Location: '.$_product->getProductUrl());
			exit;
        } elseif (Mage::getStoreConfig('multipledeals/configuration/enabled')) {	
			if (Mage::getStoreConfig('multipledeals/configuration/notify')) {			
				$mail = new Zend_Mail();
				$content = 'A customer tried to view the deals page.';
				$mail->setBodyHtml($content);
				$mail->setFrom('customer@multipledeals.com');
				$mail->addTo(Mage::getStoreConfig('multipledeals/configuration/admin_email'));
				$mail->setSubject('There are no deals setup at the moment.');	
				$mail->send();
			}
			$this->_redirect('multipledeals/index/list');
        } else {
			$this->_redirect('no-route');		
		}
    }
	
	public function listAction()
    {      
		Mage::getModel('multipledeals/multipledeals')->refreshDeals();
		$this->loadLayout();		
		
		$this->getLayout()->getBlock('breadcrumbs')
			->addCrumb('home',
			array('label'=>Mage::helper('catalogsearch')->__('Home'),
				'title'=>Mage::helper('catalogsearch')->__('Home'),
				'link'=>Mage::getBaseUrl())
			)
			->addCrumb('multiple_deals',
			array('label'=>Mage::helper('catalogsearch')->__('All Deals'),
				'title'=>Mage::helper('catalogsearch')->__('All Deals'),)
			);
			
		$this->renderLayout();      
    }
	
	public function recentAction()
    {      
        if (Mage::getStoreConfig('multipledeals/configuration/past_deals')) {
			Mage::getModel('multipledeals/multipledeals')->refreshDeals();
			$this->loadLayout();		
			
			$this->getLayout()->getBlock('breadcrumbs')
				->addCrumb('home',
				array('label'=>Mage::helper('catalogsearch')->__('Home'),
					'title'=>Mage::helper('catalogsearch')->__('Home'),
					'link'=>Mage::getBaseUrl())
				)
				->addCrumb('multiple_deals_recent',
				array('label'=>Mage::helper('catalogsearch')->__('Recent Deals'),
					'title'=>Mage::helper('catalogsearch')->__('Recent Deals'),)
				);
				
			$this->renderLayout();
		} else {
			$this->_redirect('no-route');		
		}
    }
}