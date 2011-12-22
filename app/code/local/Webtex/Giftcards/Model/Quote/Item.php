<?php
class Webtex_Giftcards_Model_Quote_Item extends Mage_Sales_Model_Quote_Item
{
    public function setProduct($product)
    {
    	$new = clone $product;
    	parent::setProduct($new);
    	if($new->getTypeId() == 'giftcards'){
    		$custom = $new->getCustomOptions();
    		if (isset($custom['option_0'])) {
    			$this->getProduct()->setPrice($custom['option_0']->getValue());	
    		}
        }
        return $this;
    }
    
    public function getProduct() 
    {
    	$product = parent::getProduct();
    	if ($product->getTypeId() == 'giftcards') {
    		$custom = $product->getCustomOptions();
    		if (isset($custom['option_1'])) {
    			if ($custom['option_1']->getValue() == 'E') {
    				if (strpos($product->getName(), '(E-mail)') === false)
    				$product->setName($product->getName() . '(E-mail)');
    			} else {
    				if (strpos($product->getName(), '(Print)') === false)
    				$product->setName($product->getName() . '(Print)');
    			}
    		}
    	}
    	return $product;
    }
}