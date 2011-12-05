<?php
	class Turnkeye_Testimonial_Block_Adminhtml_Testimonial_Edit_Form extends Mage_Adminhtml_Block_Widget_Form
	{
		protected function _prepareLayout()
		{
			parent::_prepareLayout();
			if (Mage::getSingleton('cms/wysiwyg_config')->isEnabled()) {
				$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
			}
		}
		
		protected function _prepareForm()
		{
			$form = new Varien_Data_Form(array(
										  'id' => 'edit_form',
										  'action' => $this->getUrl('*/*/save', array('id' => $this->getRequest()->getParam('id'))),
										  'method' => 'post',
										  'enctype' => 'multipart/form-data'
				));
		  
			$fieldset = $form->addFieldset('testimonial_form', array(
				'legend'	  => Mage::helper('testimonial')->__('Testimonial'),
				'class'		=> 'fieldset-wide',
			  )
			);

                        $fieldset->addField('testimonial_position', 'text', array(
								'name'      => 'testimonial_position',
								'label'     => Mage::helper('testimonial')->__('Position'),
								'style'     => 'width:100px !important',
                        ));


                        $fieldset->addField('testimonial_img', 'image', array(
                                'name'      => 'testimonial_img',
                                'label'     => Mage::helper('testimonial')->__('Image'),
                        ));


			$fieldset->addField('testimonial_name', 'text', array(
				'name'      => 'testimonial_name',
				'label'     => Mage::helper('testimonial')->__('Name'),
				'class'     => 'required-entry',
				'required'  => true,
			));

			$fieldset->addField('testimonial_text', 'editor', array(
				'name'      => 'testimonial_text',
				'label'     => Mage::helper('testimonial')->__('Text'),
				'title'     => Mage::helper('testimonial')->__('Text'),
				'style'     => 'width:100%;height:300px;',
				'required'  => true,
				'config'    => Mage::getSingleton('testimonial/wysiwyg_config')->getConfig()
			));

			$fieldset->addField('testimonial_sidebar', 'select', array(
				'label'     => Mage::helper('testimonial')->__('Show in sidebox'),
				'name'      => 'testimonial_sidebar',
				'values'    => array(
					array(
						'value'     => 1,
						'label'     => Mage::helper('testimonial')->__('Yes'),
					),
					array(
						'value'     => 0,
						'label'     => Mage::helper('testimonial')->__('No'),
					),
				),
			)); 

			if (Mage::registry('testimonial')) {
			  $form->setValues(Mage::registry('testimonial')->getData());
			}

			$form->setUseContainer(true);
			$this->setForm($form);
			return parent::_prepareForm();
		}
	}