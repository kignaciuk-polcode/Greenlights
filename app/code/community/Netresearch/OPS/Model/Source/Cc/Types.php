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
 * OPS credit card types
 */
class Netresearch_OPS_Model_Source_Cc_Types
{
    protected $types = array(
        'AIRPLUS',
        'American Express',
        'Aurora',
        'Aurore',
        'Billy',
        'BCMC',
        'CB',
        'Cofinoga',
        'Dankort',
        'Diners Club',
        'JCB',
        'Maestro',
        'MaestroUK',
        'MasterCard',
        'NetReserve',
        'PRIVILEGE',
        'PostFinance + card',
        'Solo',
        'UATP',
        'UNEUROCOM',
        'VISA',
    );
    
    /**
     * @return array
     */
    public function toOptionArray()
    {
        $options = array();
        foreach ($this->types as $type) {
            $options[] = array(
                'value' => $type,
                'label' => Mage::helper('ops')->__($type)
            );
        }
        return $options;
    }
}
