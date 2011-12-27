<?php

class Polcode_Offer_Block_Inquiry_Totals extends Mage_Core_Block_Template {

    protected $_totals;
    protected $_inquiry = null;

    protected function _beforeToHtml() {
        $this->_initTotals();
        foreach ($this->getChild() as $child) {
            if (method_exists($child, 'initTotals')) {
                $child->initTotals();
            }
        }
        return parent::_beforeToHtml();
    }

    public function getInquiry() {
        if ($this->_inquiry === null) {
            if ($this->hasData('inquiry')) {
                $this->_inquiry = $this->_getData('inquiry');
            } elseif (Mage::registry('current_inquiry')) {
                $this->_inquiry = Mage::registry('current_inquiry');
            } elseif ($this->getParentBlock()->getInquiry()) {
                $this->_inquiry = $this->getParentBlock()->getInquiry();
            }
        }
        return $this->_inquiry;
    }

    public function getSource() {
        return $this->getInquiry();
    }

    protected function _initTotals() {
        $source = $this->getSource();

        $this->_totals = array();
        $this->_totals['subtotal'] = new Varien_Object(array(
                    'code' => 'subtotal',
                    'value' => $source->getSubtotal(),
                    'label' => $this->__('Subtotal')
                ));

        return $this;
    }
 
    public function getTotals($area=null)
    {
        $totals = array();
        if ($area === null) {
            $totals = $this->_totals;
        } else {
            $area = (string)$area;
            foreach ($this->_totals as $total) {
                $totalArea = (string) $total->getArea();
                if ($totalArea == $area) {
                    $totals[] = $total;
                }
            }
        }
        return $totals;
    }    
    
    public function formatValue($total)
    {
        return Mage::helper('core')->currency($total->getValue());

    }     
    

}
