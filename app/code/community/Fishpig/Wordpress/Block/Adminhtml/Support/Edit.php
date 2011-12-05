<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Adminhtml_Support_Edit extends Mage_Adminhtml_Block_Widget_Form_Container
{
    public function __construct()
    {
        parent::__construct();
                 
        $this->_objectId = 'id';
        $this->_blockGroup = 'wordpress';
        $this->_controller = 'adminhtml_support';
        $this->_buttons = array();
    }

    public function getHeaderText()
    {
       return $this->__('Fishpig\'s Magento WordPress Integration Extension');
    }
}