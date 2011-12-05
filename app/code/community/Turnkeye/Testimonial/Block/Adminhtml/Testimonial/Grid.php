<?php
	class Turnkeye_Testimonial_Block_Adminhtml_Testimonial_Grid extends Mage_Adminhtml_Block_Widget_Grid
	{

		public function __construct()
		{
			parent::__construct();
			$this->setId('testimonialGrid');
			$this->setDefaultSort('testimonial_position');
			$this->setDefaultDir('ASC');
			$this->setSaveParametersInSession(true);
		}

		protected function _prepareCollection()
		{
			$this->setCollection(Mage::getModel('testimonial/testimonial')->getCollection());
			return parent::_prepareCollection();
		}

		protected function _prepareColumns()
		{
                        $this->addColumn('testimonial_position', array(
                                'header'    => Mage::helper('testimonial')->__('Position'),
                                'align'     => 'right',
                                'width'     => '50px',
                                'index'     => 'testimonial_position',
                                'type'      => 'number',
                        ));

			$this->addColumn('testimonial_name', array(
				'header'    => Mage::helper('testimonial')->__('Name'),
				'align'     => 'left',
				'index'     => 'testimonial_name',
			));

			$this->addColumn('testimonial_text', array(
				'header'    => Mage::helper('testimonial')->__('Text'),
				'align'     => 'left',
				'index'     => 'testimonial_text',
			));

			
			
			return parent::_prepareColumns();
		}

		public function getRowUrl($row)
		{
			return $this->getUrl('*/*/edit', array('id' => $row->getId()));
		}

	}
