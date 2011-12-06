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
 * Do not edit or add to this file if you wish to upgrade this Module to newer
 * versions in the future.
 *
 * @category   Netzarbeiter
 * @package    Netzarbeiter_GroupsCatalog
 * @copyright  Copyright (c) 2011 Vinai Kopp http://netzarbeiter.com
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Backend model for attribute with multiple values, Netzarbeiter_GroupsCatalog version
 *
 * @category   Netzarbeiter
 * @package    Netzarbeiter_GroupsCatalog
 * @author     Vinai Kopp <vinai@netzarbeiter.com>
 */
class Netzarbeiter_GroupsCatalog_Model_Entity_Attribute_Backend_Customergroups
	extends Mage_Eav_Model_Entity_Attribute_Backend_Abstract
{
	/**
	 * The name of the db field to grow (of the attribute value table)
	 *
	 * @var string
	 */
	protected $_dbFieldName = 'value';

	/**
	 * When growing the field length, make the new length a multiple of this factor
	 *
	 * @var int
	 */
	protected $_dbFieldLengthFactor = 512;

	/**
	 * Prepare the customer groups selecton array before saving, and clear the layered navigation cache if needed.
	 *
	 * @param Mage_Catalog_Model_Product $object
	 * @return nothing afaik :)
	 */
    public function beforeSave($object)
    {
        $data = $object->getData($this->getAttribute()->getAttributeCode());
        $helper = Mage::helper('groupscatalog');
        
        /**
         * Default to using the default - don't let the customer select nothing
         */
        if (is_null($data) || '' === $data) {
        	$data = array(Netzarbeiter_GroupsCatalog_Helper_Data::USE_DEFAULT);
        }
        elseif (is_array($data) && count($data) > 1) {
        	if (in_array(Netzarbeiter_GroupsCatalog_Helper_Data::USE_DEFAULT, $data))
       		{
        		/**
        		 * remove the "use default" value if other groups are selcted with it
        		 */
        		$data_tmp = array();
        		foreach ($data as $v) if ($v != Netzarbeiter_GroupsCatalog_Helper_Data::USE_DEFAULT) $data_tmp[] = $v;
        		$data = $data_tmp;
        		Mage::getSingleton('adminhtml/session')->addNotice(
        			$helper->__('The USE DEFAULT selection was ignored because you also selected other customer groups.')
        		);
        	}
        	if (in_array(Netzarbeiter_GroupsCatalog_Helper_Data::NONE, $data))
       		{
        		/**
        		 * remove all groups but the "none" value
        		 */
        		$data = array(Netzarbeiter_GroupsCatalog_Helper_Data::NONE);
        		Mage::getSingleton('adminhtml/session')->addNotice(
        			$helper->__('Customer groups besides NONE where removed from the selection.')
        		);
        	}
        }

		if (is_array($data))
		{
			$data = implode(',', $data);
		}
        $object->setData($this->getAttribute()->getAttributeCode(), $data);

        try
        {
            $this->checkDbFieldLength($object);

            /**
             * Check if the groupscatalog config was changed. If yes, clear the layered navigation cache.
             * This only applies to Magento Version 1.2.0 and above.
             */
            if (version_compare(Mage::getVersion(), '1.2', '>=') && $object instanceof Mage_Catalog_Model_Product)
            {
                $_product = Mage::getModel('catalog/product')->setId($object->getId());
                $oldData = $helper->getGroupsCatalogAttributeArray($_product);
                if ($oldData != $data)
                {
                    $this->_getAggregator()->clearProductData($object->getId());
                }
            }

            /*
             * Not needed - layered cache seems to get cleared anyway on category saves
            elseif ($object instanceof Mage_Catalog_Model_Category)
            {
                $_category = Mage::getModel('catalog/category')->setId($object->getId());
                $oldData = $helper->getGroupsCatalogAttributeArray($_category);
                if ($oldData != $data)
                {
                    $tags = array(
                        Mage_Catalog_Model_Category::CACHE_TAG.':'.$category->getPath()
                    );
                    $this->_getAggregator()->clearCacheData($tags);
                }
            }
            */
        }
        catch (Exception $e)
        {
            /*
             * All of the contents of this try-catch blocks isn't available during
             * imports with the ImportExport module. I don't see any clean way to
             * check if this method is called during such an import, so for now,
             * because all that happens is housekeeping here, just silently
             * ignore errors.
             */
        }

        return parent::beforeSave($object);
    }

	public function validate($object)
	{
		$attrCode = $this->getAttribute()->getAttributeCode();
		$value = $object->getData($attrCode);
		if (is_null($value) || '' === $value)
		{
			$label = $this->getAttribute()->getFrontend()->getLabel();
			Mage::throwException(Mage::helper('eav')->__('The value of attribute "%s" must be set.', $label));
		}
		return true;
	}
    
    protected function _getAggregator()
    {
    	return Mage::getSingleton('catalogindex/aggregation');
    }

	public function checkDbFieldLength($object)
	{
        if ($resource = $object->getResource())
        {
            $attributeCode = $this->getAttribute()->getAttributeCode();
            $requiredLength = strlen($object->getData($attributeCode));
            $adapter = $resource->getWriteConnection();
            $fieldLength = $this->getDbFieldLength($adapter);
            if ($fieldLength -2 < $requiredLength)
            {
                if (! Mage::helper('groupscatalog')->getConfig('grow_db_field'))
                {
                    $this->_warnDbFieldLength($fieldLength, $requiredLength);
                    return;
                }
                $newLength = $this->_getNewDbFieldLength($requiredLength);
                $definition = sprintf("VARCHAR(%d) NOT NULL DEFAULT ''", $newLength);
                $adapter->modifyColumn($this->getTable(), $this->_dbFieldName, $definition);
            }
        }
	}

	public function getDbFieldLength($adapter = null)
	{
		if (! isset($adapter))
		{
			$adapter = Mage::getResourceModel('catalog/product')->getWriteConnection();
		}
		$info = $adapter->describeTable($this->getTable());
		return $info[$this->_dbFieldName]['LENGTH'];
	}

	protected function _getNewDbFieldLength($requiredLength)
	{
		return (floor($requiredLength / $this->_dbFieldLengthFactor) +1) * $this->_dbFieldLengthFactor;
	}

	protected function _warnDbFieldLength($fieldLength, $requiredLength)
	{
		Mage::getSingleton('adminhtml/session')->addError(
			Mage::helper('groupscatalog')->__('The db field size is %s bytes to small to save the group permissions. Please enable the configuration setting to dynamicaly grow the field length and try again.',
				($requiredLength - $fieldLength > 0 ? $requiredLength - $fieldLength : 0)
			)
		);
	}
}
