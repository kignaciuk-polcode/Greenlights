<?php 

class Polcode_Offer_Block_Adminhtml_Inquiry_View_Tab_Info
    extends Mage_Adminhtml_Block_Widget
    implements Mage_Adminhtml_Block_Widget_Tab_Interface
{

    public function getInquiry()
    {
        return Mage::registry('current_inquiry');
    }


//    public function getSource()
//    {
//        return $this->getOrder();
//    }



    public function getItemsHtml()
    {
        return $this->getChildHtml('inquiry_items');
    }



    public function getViewUrl($orderId)
    {
        return $this->getUrl('*/*/*', array('order_id'=>$orderId));
    }

    /**
     * ######################## TAB settings #################################
     */
    public function getTabLabel()
    {
        return Mage::helper('offer')->__('Information');
    }

    public function getTabTitle()
    {
        return Mage::helper('offer')->__('Inquiry Information');
    }

    public function canShowTab()
    {
        return true;
    }

    public function isHidden()
    {
        return false;
    }
}
