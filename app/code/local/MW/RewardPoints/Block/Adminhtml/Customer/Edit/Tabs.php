<?php
class MW_RewardPoints_Block_Adminhtml_Customer_Edit_Tabs extends Mage_Adminhtml_Block_Customer_Edit_Tabs
{
    protected function _beforeToHtml()
    {

        $this->addTab('rewardpoints', array(
            'label'     => Mage::helper('rewardpoints')->__('Reward Points'),
            'content'   => $this->getLayout()->createBlock('rewardpoints/adminhtml_customer_edit_tab_rewardpoints')->initForm()->toHtml(),
            'active'    => Mage::registry('current_customer')->getId() ? false : true
        ));
        if(Mage::helper('rewardpoints')->getCreditModule()){
			$this->addTab('credit', array(
            	'label'     => Mage::helper('credit')->__('Credit'),
            	'content'   => $this->getLayout()->createBlock('credit/adminhtml_customer_edit_tab_credit')->initForm()->toHtml()
        	));
        }

        $this->_updateActiveTab();
        Varien_Profiler::stop('customer/tabs');
        return parent::_beforeToHtml();
    }
}
