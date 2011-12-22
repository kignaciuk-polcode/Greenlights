<?php
class Webtex_Giftcards_Model_Product_Type_Giftcards extends Mage_Catalog_Model_Product_Type_Abstract
{
    const TYPE_CODE = 'giftcards';

    public function prepareForCartAdvanced(Varien_Object $buyRequest, $product = null, $processMode = null)
    {
        if (!$processMode) {
            $processMode = self::PROCESS_MODE_FULL;
        }
        $_products = $this->_prepareProduct($buyRequest, $product, $processMode);
        return $_products;
    }

    protected function _prepareProduct(Varien_Object $buyRequest, $product, $processMode)
    {
        $product = $this->getProduct($product);

        $data = $buyRequest->getData();

        if (!isset($data['email-print']) || !$data['email-print']) {
            return $this->getSpecifyOptionsMessage();
        }

        /* @var Mage_Catalog_Model_Product $product */
        // try to add custom options
        try {
            $options = $this->_prepareOptions($buyRequest, $product, $processMode);
        } catch (Mage_Core_Exception $e) {
            return $e->getMessage();
        }

        if (is_string($options)) {
            return $options;
        }
        // try to found super product configuration
        // (if product was buying within grouped product)
        $superProductConfig = $buyRequest->getSuperProductConfig();
        if (!empty($superProductConfig['product_id'])
            && !empty($superProductConfig['product_type'])
        ) {
            $superProductId = (int) $superProductConfig['product_id'];
            if ($superProductId) {
                if (!$superProduct = Mage::registry('used_super_product_'.$superProductId)) {
                    $superProduct = Mage::getModel('catalog/product')->load($superProductId);
                    Mage::register('used_super_product_'.$superProductId, $superProduct);
                }
                if ($superProduct->getId()) {
                    $assocProductIds = $superProduct->getTypeInstance(true)->getAssociatedProductIds($superProduct);
                    if (in_array($product->getId(), $assocProductIds)) {
                        $productType = $superProductConfig['product_type'];
                        $product->addCustomOption('product_type', $productType, $superProduct);

                        $buyRequest->setData('super_product_config', array(
                            'product_type' => $productType,
                            'product_id'   => $superProduct->getId()
                        ));
                    }
                }
            }
        }

        $product->prepareCustomOptions();
        $buyRequest->unsetData('_processing_params'); // One-time params only
        $product->addCustomOption('info_buyRequest', serialize($buyRequest->getData()));
        if ($data['email-print'] == 'email') {
            $product->addCustomOption('option_0', $data['amount']);
            $product->addCustomOption('option_1', 'E');
            $product->addCustomOption('option_2', $data['email-to']);
            $product->addCustomOption('option_3', $data['email-from']);
            $product->addCustomOption('option_4', $data['email-message']);
            $product->addCustomOption('option_5', $data['day-to-send']);
            $product->addCustomOption('option_6', $data['recipient']);
        } else if ($data['email-print'] == 'print') {
            $product->addCustomOption('option_0', $data['print-amount']);
            $product->addCustomOption('option_1', 'P');
            $product->addCustomOption('option_2', $data['print-to']);
            $product->addCustomOption('option_3', $data['print-from']);
            $product->addCustomOption('option_4', $data['print-message']);
        }
        $product->setPrice($data['amount']);
        // set quantity in cart
        if ($this->_isStrictProcessMode($processMode)) {
            $product->setCartQty($buyRequest->getQty());
        }
        $product->setQty($buyRequest->getQty());

        return array($product);
    }
    
    public function isVirtual($product = null)
    {
        return true;
    }

    public function getSpecifyOptionsMessage()
    {
        return Mage::helper('catalog')->__('Please specify the product\'s option(s).');
    }
}
