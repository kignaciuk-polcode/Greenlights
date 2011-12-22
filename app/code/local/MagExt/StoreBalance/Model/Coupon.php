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
 * @package    MagExt_StoreBalance
 * @copyright  Copyright (c) 2010 MagExtension (http://www.magextension.com/)
 * @license    http://www.magextension.com/LICENSE.txt End-User License Agreement
 */
 
/**
 * Coupon Model
 *
 * @author  MagExtension Development team
 */
class MagExt_StoreBalance_Model_Coupon extends Mage_Core_Model_Abstract
{
	const STATUS_ACTIVE = 1;
	const STATUS_INACTIVE = 0;
	
	const USED_YES = 1;
	const USED_NO = 0;
		
	protected $_eventPrefix	= 'mgxstorebalance_coupon';
	protected $_eventObject  = 'coupon';
	
	protected function _construct()
	{
		$this->_init('mgxstorebalance/coupon');
	}
	
	/**
	 * Loads data using string hash value
	 * 
	 * @param string $coupon Hash string
	 * @return Mage_Core_Model_Abstract
	 */
	public function loadByHash($coupon)
	{
	    return $this->load($coupon, 'hash');
	}
	
    /**
     * Validates data for coupon
     * 
     * @param Varien_Object $object
     * @returns boolean|array - returns true if validation passed successfully. Array with error
     * description otherwise
     */
    public function validateData(Varien_Object $object)
    {
        if($object->getData('from_date') && $object->getData('to_date')){
            $dateStartUnixTime = strtotime($object->getData('from_date'));
            $dateEndUnixTime   = strtotime($object->getData('to_date'));

            if ($dateEndUnixTime < $dateStartUnixTime) {
                return array(Mage::helper('rule')->__("End Date should be greater than Start Date"));
            }
        }
        return true;
    }
    
    /**
     * Set proper object data from POST data
     * 
     * @param array $couponData
     */
    public function loadPost(array $couponData)
    {
        if (!empty($couponData['details']))
        {
            foreach ($couponData['details'] as $key => $value)
            {
                /**
                 * convert dates into Zend_Date
                 */
                if (in_array($key, array('from_date', 'to_date')) && $value) {
                    $value = Mage::app()->getLocale()->date(
                        $value,
                        Varien_Date::DATE_INTERNAL_FORMAT,
                        null,
                        false
                    );
                }
                $this->setData($key, $value);
            }
        }
        if ($this->getIsNew())
        {
            $dataSettings = array_key_exists('settings', $couponData) ? $couponData['settings'] : array();
            if (array_key_exists('use_config', $couponData) && is_array($couponData['use_config']))
            {
                foreach ($couponData['use_config'] as $settingCode=>$value) {
                    $dataSettings[$settingCode] = null;
                }
            }
            $this->setData('generate', $dataSettings);
        }
    }
	
    /**
     * (non-PHPdoc)
     * @see app/code/core/Mage/Core/Model/Mage_Core_Model_Abstract#_beforeSave()
     */
	protected function _beforeSave()
	{
        if ($this->getIsNew())
	    {
	        $this->_defineCoupon();
	    }
	    else 
	    {
	    	if ($this->getIsUsed())
	    	{
	    	    $this->setUsedDate(now());
                $this->getHistoryModel()->setAction(MagExt_StoreBalance_Model_Coupon_History::ACTION_USED);
	    	}
	    }
	    
        //set history data
	    if ($this->getIsNew())
	    {
	        $this->getHistoryModel()->setAction(MagExt_StoreBalance_Model_Coupon_History::ACTION_CREATED);
	    }
	    elseif (!$this->getHistoryModel()->hasAction())
	    {
	        $this->getHistoryModel()->setAction(MagExt_StoreBalance_Model_Coupon_History::ACTION_UPDATED);
	    }
	    $this->getHistoryModel()->setBalance($this->getBalance());
	    return parent::_beforeSave();
	}
	
    /**
     * (non-PHPdoc)
     * @see app/code/core/Mage/Core/Model/Mage_Core_Model_Abstract#_afterSave()
     */
	protected function _afterSave()
	{
	    parent::_afterSave();
	    $this->getHistoryModel()->unsetData();
	    return $this;
	}
	
	/**
	 * Create numerous coupons
	 */
	public function generate()
	{
	    $qty = (int)$this->getData('qty');
	    $startData = $this->getData();
	    for ($i = 0; $i < $qty; $i++)
	    {
	        $this->setData($startData);
	        $this->unsetData($this->getIdFieldName());
	        $this->save();
	    }
	}
	
	/**
	 * Define randomly generated value for new coupon
	 */
	protected function _defineCoupon()
	{
	    $couponGenerator = Mage::getSingleton('mgxstorebalance/coupon_generator');
        /* @var $couponGenerator MagExt_StoreBalance_Model_Coupon_Generator */
	    
	    $generate = $this->getGenerate();
	    if (is_array($generate))
	    {
	        foreach ($generate as $setting => $value)
	        {
	            if (is_null($value))
	            {
	                $generate[$setting] = Mage::getStoreConfig('magext_storebalance/storebalance_coupons/'.$setting);
	            }
	        }
	    }
        $couponGenerator->setLength((int)$generate['coupon_length'])
            ->setBlockSize((int)$generate['group_length'])
            ->setBlockSeparator((string)$generate['group_separator']);
            
        switch ($generate['coupon_format'])
        {
            default:
            case 'alphanum':
                $couponGenerator->setUseNumbers(true)
                    ->setUseBig(true)
                    ->setUseSmall(false);
                break;
            case 'num':
                $couponGenerator->setUseNumbers(true)
                    ->setUseBig(false)
                    ->setUseSmall(false);
                break;
            case 'alphabet':
                $couponGenerator->setUseNumbers(false)
                    ->setUseBig(true)
                    ->setUseSmall(false);
                break;
        }          
        $generatedHash = $couponGenerator->generate();
        $this->setHash($generatedHash); 
        return $this;
	}
	
	/**
	 * If coupon used earlier
	 * @return boolean
	 */
	public function isUsed()
	{
	    return (bool)$this->getUsedDate();
	}
	
	/**
	 * If coupon is active on current date
	 * @return boolean
	 */
	public function isActive()
	{
        $curDate  = Mage::app()->getLocale()->date()->toString(Varien_Date::DATE_INTERNAL_FORMAT);
        $fromDate = $this->getFromDate();
	    $toDate   = $this->getToDate();
	    
	    if ($fromDate && $curDate < $fromDate)
	       return false;
	       
        if ($toDate && $curDate > $toDate)
            return false;
            
        if (!$this->getIsActive())
            return false;

        return true;
	}
	
	/**
	 * If coupon can be used on specified website
	 * @param int|null $websiteId
	 * @return boolean
	 */
	public function checkWebsite($websiteId = null)
	{
        $websiteId = Mage::app()->getWebsite($websiteId)->getId();
        return $this->getWebsiteId() == $websiteId;
	}
	
	/**
	 * If coupon balance is positive
	 * @return boolean
	 */
	public function checkBalance()
	{
	    return $this->getBalance() > 0;
	}
	
	/**
	 * Checks if coupon is valid to use for recharging balance
	 * @return boolean
	 */
	public function isValidForUse()
	{
	    if ( !$this->isActive() || 
	         //$this->isUsed() || 
	         !$this->checkWebsite() || 
	         !$this->checkBalance() )
	    {
	        Mage::throwException(Mage::helper('mgxstorebalance')->__('Store Balance Coupon is invalid.'));
	    }
	    return true;
	}
	
	/**
	 * If coupon can be deleted
	 * @return boolean
	 */
	public function isDeletable()
	{
	    return true;
	}
	
	/**
	 * Redeems Store Balance Coupon and refills Store Balance
	 */
	public function useCoupon()
	{
	    if (!$this->isValidForUse())
            return $this;

        if (!($customerId = Mage::getSingleton('customer/session')->getCustomerId()))
        {
            Mage::throwException(Mage::helper('mgxstorebalance')->__('Customer ID is not set'));
        }
        $this->setCustomerId($customerId);
        
        Mage::getModel('mgxstorebalance/balance')->refill($this);
        
        $this->setBalance(0)
             ->setIsUsed(true)
             ->setIsActive(0)
             ->save();
	}
	
	/**
     * Retreive history model instance
     *
     * @return MagExt_StoreBalance_Model_Coupon_History
     */
    public function getHistoryModel()
    {
        if (!$this->hasData('history_model'))
        {
            $this->setHistoryModel(Mage::getModel('mgxstorebalance/coupon_history'));
        }
        return $this->getData('history_model');
    }
    
    /**
     * Retrieve status option array
     *
     * @return array
     */
    static public function getStatusOptionArray($addEmptyItemValue = false)
    {
        $options = array();
        if ($addEmptyItemValue !== false)
            $options = array($addEmptyItemValue => '');
        $options = $options + array(
            self::STATUS_ACTIVE   => Mage::helper('mgxstorebalance')->__('Active'),
            self::STATUS_INACTIVE => Mage::helper('mgxstorebalance')->__('Inactive'),
        );
        return $options;
    }
    
    /**
     * Update status value for coupon
     *
     * @param   int $couponId
     * @param   int $storeId
     * @param   int $value
     * @return  MagExt_StoreBalance_Model_Coupon
     */
    public function updateCouponStatus($couponId, $value)
    {
        $this->load($couponId)->setIsActive($value)->save();
        Mage::dispatchEvent($this->_eventPrefix.'_status_update', array(
            $this->_eventObject =>$this,
            'coupon_id'         => $couponId,
            'status'            => $value
        ));
        return $this;
    }
}