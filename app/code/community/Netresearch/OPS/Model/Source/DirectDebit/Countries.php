<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Netresearch_OPS
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * OPS direct debit countries
 */
class Netresearch_OPS_Model_Source_DirectDebit_Countries
{
    protected $countries = array(
        'DE',
        'AT',
        'NL',
    );

    protected $options = array();
    
    /**
     * @return array
     */
    public function toOptionArray($isMultiselect=false)
    {
        if (!$this->options) {
            $this->options = Mage::getResourceModel('directory/country_collection')->loadData()->toOptionArray(false);
            if(!$isMultiselect){
                array_unshift($this->options, array('value'=>'', 'label'=> Mage::helper('adminhtml')->__('--Please Select--')));
            }
            foreach ($this->options as $offset=>$option) {
                if (!in_array($option['value'], $this->countries)) {
                    unset($this->options[$offset]);
                }
            }
        }

        return $this->options;
    }
}
