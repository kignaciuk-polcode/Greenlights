<?php
/**
 * Webtex
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the EULA that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.webtexsoftware.com/LICENSE.txt
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to info@webtexsoftware.com and we will send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension to newer
 * versions in the future. If you wish to customize the extension for your
 * needs please refer to http://www.webtexsoftware.com for more information, 
 * or contact us through this email: info@webtexsoftware.com.
 *
 * @category   Webtex
 * @package    Webtex_Core
 * @copyright  Copyright (c) 2011 Webtex Solutions, LLC (http://www.webtexsoftware.com/)
 * @license    http://www.webtexsoftware.com/LICENSE.txt End-User License Agreement
 */

class Webtex_Core_Abstract_Adminhtml_Controller_Action extends Mage_Adminhtml_Controller_Action
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