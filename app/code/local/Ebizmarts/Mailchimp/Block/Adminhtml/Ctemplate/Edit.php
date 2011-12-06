<?php

class Ebizmarts_Mailchimp_Block_Adminhtml_Ctemplate_Edit extends Mage_Adminhtml_Block_Widget_Form_Container{

    public function __construct(){

        parent::__construct();
        $this->_objectId = 'id';
        $this->_blockGroup = 'mailchimp';
        $this->_controller = 'adminhtml_ctemplate';
        $helper = Mage::helper('mailchimp');

		$this->_addButton('saveandcontinue', array(
		            'label'     => Mage::helper('adminhtml')->__('Save And Continue Edit'),
		            'onclick'   => 'saveAndContinueEdit()',
		            'class'     => 'save',
		    		), -100);

		if(Mage::registry('ctemplate_data')){
			if(Mage::registry('ctemplate_data')->getTid() != 'user'){
				$this->_removeButton('save');
				$this->_removeButton('delete');
				$this->_removeButton('saveandcontinue');
			}else{
				if(Mage::registry('ctemplate_data')->getActive() == 'Y'){
					$this->_updateButton('delete', 'label', $helper->__('Deactivate Template'));
				}else{
					$this->_removeButton('delete');
					$this->_addButton('reactivate', array(
			            'label'     => Mage::helper('adminhtml')->__('Reactivate Template'),
			            'onclick'   => 'deleteConfirm(\''. Mage::helper('adminhtml')->__('Are you sure you want to do this?')
	                    				.'\', \'' . $this->getReactivateUrl() . '\')',
			            'class'     => 'add',
			        ), 0);
				}
			}
		}else{
			$this->_removeButton('delete');
		}

		if(Mage::registry('ctemplate_data') && Mage::registry('ctemplate_data')->getPreviewImage()){
			$this->_formScripts[] = "
				Event.observe(window, 'load', function(event) {
					if($('previewlink_text')){
						var a = new Element('img', {src:'".Mage::registry('ctemplate_data')->getPreviewImage()."', width: '200', height: '134', id:'prev_img' });
						$('previewlink_text').insert(a);
					}
				});
			";
		}else{
			$this->_formScripts[] = "
				Event.observe(window, 'load', function(event) {
					if($('previewlink_text')){
						$('previewlink_text').innerHTML = '".Mage::helper('mailchimp')->__('Preview source on popup')."';
					}
				});
			";
		}

    }

    private function getReactivateUrl(){

        return $this->getUrl('*/*/reactivate', array($this->_objectId => $this->getRequest()->getParam($this->_objectId)));
    }

    public function getHeaderText(){

        if(Mage::registry('ctemplate_data')){
        	if(Mage::registry('ctemplate_data')->getTid() == 'user'){
        		return Mage::helper('mailchimp')->__('Edit Campaign Template');
        	}
            return Mage::helper('mailchimp')->__('View Campaign Template');
        } else {
            return Mage::helper('mailchimp')->__('Add a new Campaign Template');
        }
    }

}