    <?php
     
    class Polcode_Offer_Block_Adminhtml_Inquiry_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
    {
        public function __construct()
        {
            parent::__construct();
                   
            $this->_objectId = 'id';
            $this->_blockGroup = 'inquiry';
            $this->_controller = 'adminhtml_inquiry';
     
            $this->_updateButton('save', 'label', Mage::helper('offer/inquiry')->__('Save Item'));
            $this->_updateButton('delete', 'label', Mage::helper('offer/inquiry')->__('Delete Item'));
        }
     
        public function getHeaderText()
        {
            if( Mage::registry('inquiry_data') && Mage::registry('inquiry_data')->getId() ) {
                return Mage::helper('offer/inquiry')->__("Edit Inquiry '%s'", $this->htmlEscape(Mage::registry('inquiry_data')->getId()));
            } else {
                return Mage::helper('offer/inquiry')->__('Add Item');
            }
        }
    }
