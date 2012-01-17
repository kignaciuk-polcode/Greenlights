<?php

class Devinc_Multipledeals_Block_Adminhtml_Multipledeals_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'multipledeals';
        $this->_controller = 'adminhtml_multipledeals';
        
        $this->_updateButton('save', 'label', Mage::helper('multipledeals')->__('Save Deal'));
        $this->_updateButton('delete', 'label', Mage::helper('multipledeals')->__('Delete Deal'));
		$this->_removeButton('reset');
		
        $this->_addButton('saveandcontinue', array(
            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
            'onclick'   => 'saveAndContinueEdit()',
            'class'     => 'save',
        ), -100);	
		
		if ($this->getRequest()->getParam('id')!='' && $this->getRequest()->getParam('id')!=0) {
			$data = Mage::registry('multipledeals_data')->getData();
			
			if ($data['type']==2) {
				$this->_addButton('main', array(
					'label'     => Mage::helper('adminhtml')->__('Set as Main Deal'),
					'onclick'   => 'main()',
				));		
			}
		}
	
        $this->_formScripts[] = "
            function toggleEditor() {
                if (tinyMCE.getInstanceById('multipledeals_content') == null) {
                    tinyMCE.execCommand('mceAddControl', false, 'multipledeals_content');
                } else {
                    tinyMCE.execCommand('mceRemoveControl', false, 'multipledeals_content');
                }
            }
			
            function main(){
                editForm.submit($('edit_form').action.replace('/save/', '/main/'));
            }	

            function saveAndContinueEdit(){
				if (document.getElementById('product_id').value!='') {
					editForm.submit($('edit_form').action+'back/edit/');
				} else {
					document.getElementById('advice-required-entry-product_details_select').style.display = 'none';
					$('advice-required-entry-product_details_select').appear({ duration: 1 });
					if (document.getElementById('display_on').value=='' || document.getElementById('price').value=='' || document.getElementById('qty').value=='') {
						editForm.submit($('edit_form').action+'back/edit/');
					}
				}
            }
        ";
    }

    public function getHeaderText()
    {
        if( Mage::registry('multipledeals_data') && Mage::registry('multipledeals_data')->getId() ) {
            return Mage::helper('multipledeals')->__("Edit Deal");
        } else {
            return Mage::helper('multipledeals')->__('Add Deal');
        }
    }
}