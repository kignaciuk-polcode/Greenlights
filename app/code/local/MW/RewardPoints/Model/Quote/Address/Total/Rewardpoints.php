<?php
class MW_RewardPoints_Model_Quote_Address_Total_Rewardpoints extends Mage_Sales_Model_Quote_Address_Total_Abstract
{
	protected $_itemTotals;
	
	public function __construct(){
        $this->setCode('reward_points');
    }
    
	/**
     * Return item price
     *
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @return float
     */
    protected function _getItemPrice($item)
    {
        $price = $item->getDiscountCalculationPrice();
        return ($price !== null) ? $price : $item->getCalculationPrice();
    }

    /**
     * Return item base price
     *
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @return float
     */
    protected function _getItemBasePrice($item)
    {
        $price = $item->getDiscountCalculationPrice();
        return ($price !== null) ? $item->getBaseDiscountCalculationPrice() : $item->getBaseCalculationPrice();
    }
	/**
     * Return discount item qty
     *
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @return int
     */
    protected function _getItemQty($item)
    {
        return $item->getTotalQty();
    }
    
    public function initTotals($items, Mage_Sales_Model_Quote_Address $address){
    	if (!$items) {
            return $this;
        }
        $totalItemsPrice = 0;
        $totalBaseItemsPrice = 0;
        $validItemsCount = 0;
    	foreach ($items as $item) {
        //Skipping child items to avoid double calculations
        	if ($item->getParentItemId()) {
            	continue;
            }

           $qty = $this->_getItemQty($item);
           $totalItemsPrice 		+= $this->_getItemPrice($item) * $qty;
           $totalBaseItemsPrice 	+= $this->_getItemBasePrice($item) * $qty;
           $validItemsCount++;
       }

       $this->_itemTotals = array(
       		'items_price' => $totalItemsPrice,
            'base_items_price' => $totalBaseItemsPrice,
            'items_count' => $validItemsCount,
       );
    }
    
	public function collect(Mage_Sales_Model_Quote_Address $address)
    {
        parent::collect($address);
    	$quote = $address->getQuote();
        $totalDiscountAmount = $quote->getRewardpointDiscount();
        $subtotalWithDiscount= 0;
        $baseTotalDiscountAmount = $quote->getRewardpointDiscount();
        $baseSubtotalWithDiscount= 0;
    	$items = $address->getAllItems();
    	$rate = Mage::helper('core')->currency(1,false);
        if (!count($items)) {
            return $this;
        }
      /*  $this->initTotals($items, $address);
		foreach($items as $item){
			
			$baseItemPrice = $this->_getItemPrice($item);
			$qty = $this->_getItemQty($item);
			$rate = Mage::helper('core')->currency(1,false);
			$discountRate = $baseItemPrice * $qty / ($this->_itemTotals['base_items_price'] * $rate);
			$maximumItemDiscount = $totalDiscountAmount * $discountRate;
            $quoteAmount = $maximumItemDiscount;
            $baseQuoteAmount = $maximumItemDiscount/$rate;
			//echo $item->getName()."__".$maximumItemDiscount."<br>";
			$item->setDiscountAmount($quoteAmount + $item->getDiscountAmount());
			
			$item->setBaseDiscountAmount($baseQuoteAmount + $item->getBaseDiscountAmount());
			
		}*/
        $address->setRewardPointsDiscount($totalDiscountAmount);
        $address->setBaseRewardPointsDiscount($baseTotalDiscountAmount);
        
        //$address->setSubtotalWithDiscount($subtotalWithDiscount - $totalDiscountAmount);
        //$address->setBaseSubtotalWithDiscount($baseSubtotalWithDiscount - $baseTotalDiscountAmount);
		
        //echo $address->getBaseRewardPointsDiscount();
        $address->setGrandTotal($address->getGrandTotal() - $address->getRewardPointsDiscount());
        $address->setBaseGrandTotal($address->getBaseGrandTotal()-($address->getBaseRewardPointsDiscount()/$rate));
        $address->setBaseDiscountAmount($address->getBaseDiscountAmount()-($address->getBaseRewardPointsDiscount()/$rate));
        return $this;
    }

    public function fetch(Mage_Sales_Model_Quote_Address $address)
    {
        $amount = $address->getRewardPointsDiscount();
        if ($amount!=0) {
        	$title = Mage::helper('sales')->__('Discount (Reward Points)');
            $address->addTotal(array(
                'code'=> $this->getCode(),
                'title'=>$title,
                'value'=>-$amount
            ));
        }
        return $this;
    }
    

}
