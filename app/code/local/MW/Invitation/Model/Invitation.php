<?php
class MW_Invitation_Model_Invitation extends Varien_Object
{
	const BY_ID				= 1;
    const BY_EMAIL			= 2;
	

    static public function toOptionArray()
    {
        return array(
            self::BY_ID    				=> Mage::helper('invitation')->__('By Customer ID'),
            self::BY_EMAIL  			=> Mage::helper('invitation')->__('By Customer Email'),
        );
    }
}