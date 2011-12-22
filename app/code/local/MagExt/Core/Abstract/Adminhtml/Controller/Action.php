<?php
/**
 * MagExtension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MagExtension EULA 
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magextension.com/LICENSE.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@magextension.com so we can send you a copy.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to http://www.magextension.com for more information.
 *
 * @category   MagExt
 * @package    MagExt_Core
 * @copyright  Copyright (c) 2010 MagExtension (http://www.magextension.com/)
 * @license    http://www.magextension.com/LICENSE.txt End-User License Agreement
 */

class MagExt_Core_Abstract_Adminhtml_Controller_Action extends Mage_Adminhtml_Controller_Action
{
    /**
     * Convert dates with time in array from localized to internal format
     *
     * @param   array $array
     * @param   array $dateFields
     * @return  array
     */
    protected function _filterDates($array, $dateFields)
    {
        if (Mage::getVersion()>= '1.4.0.0')
            return parent::_filterDates($array, $dateFields);
            
        if (empty($dateFields)) {
            return $array;
        }

        foreach ($dateFields as $dateField) {
            if (array_key_exists($dateField, $array) && !empty($array[$dateField])) {
                $array[$dateField] = Mage::app()->getLocale()->date(
                    $array[$dateField],
                    Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
                    null,
                    false
                )->toString(Varien_Date::DATE_INTERNAL_FORMAT);
            }
        }
        return $array;
    }
}