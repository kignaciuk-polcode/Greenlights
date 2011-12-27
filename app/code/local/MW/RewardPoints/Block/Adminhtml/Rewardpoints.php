<?php
class MW_RewardPoints_Block_Adminhtml_Rewardpoints extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'adminhtml_rewardpoints';
    $this->_blockGroup = 'rewardpoints';
    $this->_headerText = Mage::helper('rewardpoints')->__('Manage Reward Points');
    parent::__construct();
    $this->_removeButton('add');$this->_removeButton('add');
    $this->_addButton('import', array(
            'label'     => Mage::helper('rewardpoints')->__('Import Reward Points'),
            'onclick'   => 'setLocation(\'' . $this->getUrl('*/*/import') .'\')',
            'class'     => 'add',
        ));
    
  }
}