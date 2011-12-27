<?php
class Webtex_Giftcards_Model_Product_Price extends Mage_Catalog_Model_Product_Type_Price
{
    protected function _applyOptionsPrice($product, $qty, $finalPrice)
    {
        return $finalPrice;
    }
}
