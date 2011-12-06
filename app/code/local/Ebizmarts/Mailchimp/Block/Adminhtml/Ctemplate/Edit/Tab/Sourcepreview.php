<?php

class Ebizmarts_Mailchimp_Block_Adminhtml_Ctemplate_Edit_Tab_Sourcepreview extends Mage_Adminhtml_Block_Widget_Form{

	protected function _prepareForm(){

    	$form = new Varien_Data_Form();
    	$helper = Mage::helper('mailchimp');
      	$this->setForm($form);
      	$fieldset = $form->addFieldset('form_sourcepreview', array('legend'=>$helper->__(Mage::registry('ctemplate_data')? 'Campaign Template Source and Preview' : 'Campaign Template Source')));

      	$fieldset->addField('source', 'textarea', array(
        	'label'     => $helper->__('HTML Source'),
          	'name'      => 'source',
          	'class'     => 'template-source',
      	));
      	$fieldset->addField('original_source', 'hidden', array(
          	'name'      => 'original_source'
      	));
      	$fieldset->addField('preview_on_popup', 'button', array(
          	'name'      => 'preview_on_popup',
          	'class'     => 'button',
          	'onclick'   => 'previewMe(\'source\')'
      	));

      	if(Mage::registry('ctemplate_data') && Mage::registry('ctemplate_data')->getDefaultContent()){
      		$fieldset->addField('dcs', 'select', array(
	        	'label'     => $helper->__(Mage::registry('ctemplate_data')->getTid() == 'user'? 'Get content from section' : 'View content by section'),
	          	'name'      => 'dcs',
	            'values'    => Mage::registry('ctemplate_data')->getDefaultContentSections(),
	            'onchange'  => 'getContentBySection(this)'
	      	));
	      	foreach(Mage::registry('ctemplate_data')->getDefaultContent() as $k=>$v){
	      		$fieldset->addField($k, 'hidden', array(
		        	'label'     => $k,
		          	'name'      => $k
		      	));
	      	}
      		$fieldset->addField('dcs_content', 'textarea', array(
	        	'label'     => $helper->__('HTML content'),
	          	'name'      => 'dcs_content',
	          	'class'     => 'template-source',
	      	));
	      	if(Mage::registry('ctemplate_data')->getTid() == 'user'){
		      	$fieldset->addField('update_section', 'button', array(
		          	'name'      => 'update_section',
		          	'class'     => 'button',
		          	'onclick'   => 'updateSource()'
		      	));
	      	}
      	}

      	if(!Mage::registry('ctemplate_data') || (Mage::registry('ctemplate_data') && Mage::registry('ctemplate_data')->getTid() == 'user')){
	      	$fieldset->addField('cms_id', 'select', array(
	        	'label'     => $helper->__('Get content from CMS Page'),
	          	'name'      => 'cms_id',
	            'values'    => Mage::getSingleton('mailchimp/source_cms')->getCmssPerStore(),
	            'onchange'  => 'getCmsContent(\'' . Mage::helper('adminhtml')->getUrl('mailchimp/adminhtml_ctemplate/cms') . '\', this)'
	      	));
	      	$fieldset->addField('cms_inlined', 'checkbox', array(
	        	'label'     => $helper->__('Get content with inlined styles [beta]'),
	          	'name'      => 'cms_inlined',
	            'checked'   => false,
	            'onclick'  => 'getCmsContent(\'' . Mage::helper('adminhtml')->getUrl('mailchimp/adminhtml_ctemplate/cms') . '\', $(\'cms_id\'))'
	      	));
      		$fieldset->addField('cms_content', 'textarea', array(
	        	'label'     => $helper->__('Full HTML content'),
	          	'name'      => 'cms_content',
	          	'class'     => 'template-source',
	      	));
	      	$fieldset->addField('preview_on_popup_cms', 'button', array(
	          	'name'      => 'preview_on_popup_cms',
	          	'class'     => 'button',
	          	'onclick'   => 'previewMe(\'cms_content\')'
	      	));
      	}

  		$default = new Varien_Object;
		$default->setPreviewOnPopup($helper->__('Preview on popup'));
		$default->setPreviewOnPopupCms($helper->__('Preview on popup'));

		if(Mage::getSingleton('adminhtml/session')->getCtemplateData()){
			$form->setValues(Mage::getSingleton('adminhtml/session')->getCtemplateData());
			$form->addValues($default->getData());
	        Mage::getSingleton('adminhtml/session')->setCtemplateData(null);
		}elseif(Mage::registry('ctemplate_data')){
			$form->setValues(Mage::registry('ctemplate_data'));
			$form->addValues($default->getData());
	  	}else{
			$form->setValues($default);
	  	}
	    return parent::_prepareForm();
  	}

}