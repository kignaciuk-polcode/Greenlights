<?php
require_once 'Mage/Checkout/controllers/CartController.php';
class Webtex_Giftcards_CartController extends Mage_Checkout_CartController
{
    public function addAction()
    {
        $cart   = $this->_getCart();
        $params = $this->getRequest()->getParams();
        try {
            if (isset($params['qty'])) {
                $filter = new Zend_Filter_LocalizedToNormalized(
                    array('locale' => Mage::app()->getLocale()->getLocaleCode())
                );
                $params['qty'] = $filter->filter($params['qty']);
            }

            $product = $this->_initProduct();
            $related = $this->getRequest()->getParam('related_product');

            /**
             * Check product availability
             */
            if (!$product) {
                $this->_goBack();
                return;
            }

            // Added for Webtex_Giftcards module
            if ($product->getTypeId() == 'giftcards'){
                if ($params['email-print'] == 'email'){
                    $product['price'] = $params['amount'];
                } else {
                    $product['price'] = $params['print-amount'];
                }

                // true only if min value is set (more than 0) and price less than min
                $min = Mage::getStoreConfig('giftcards/default/min_card_value') > 0 && $product['price'] < Mage::getStoreConfig('giftcards/default/min_card_value');
                // true only if max value is set (more than 0) and price more than max
                $max = Mage::getStoreConfig('giftcards/default/max_card_value') > 0 && $product['price'] > Mage::getStoreConfig('giftcards/default/max_card_value');
                // if one of conditions above is true than throw exception
                if ($min  || $max) {
                    Mage::throwException(Mage::helper('giftcards')->__('Card amount is not within the specified range.'));
                }
            }
            // End

            $cart->addProduct($product, $params);
            if (!empty($related)) {
                $cart->addProductsByIds(explode(',', $related));
            }

            $cart->save();

            $this->_getSession()->setCartWasUpdated(true);

            /**
             * @todo remove wishlist observer processAddToCart
             */
            Mage::dispatchEvent('checkout_cart_add_product_complete',
                array('product' => $product, 'request' => $this->getRequest(), 'response' => $this->getResponse())
            );

            if (!$this->_getSession()->getNoCartRedirect(true)) {
                if (!$cart->getQuote()->getHasError()){
                    $message = $this->__('%s was added to your shopping cart.', Mage::helper('core')->htmlEscape($product->getName()));
                    $this->_getSession()->addSuccess($message);
                }
                $this->_goBack();
            }
        } catch (Mage_Core_Exception $e) {
            if ($this->_getSession()->getUseNotice(true)) {
                $this->_getSession()->addNotice($e->getMessage());
            } else {
                $messages = array_unique(explode("\n", $e->getMessage()));
                foreach ($messages as $message) {
                    $this->_getSession()->addError($message);
                }
            }

            $url = $this->_getSession()->getRedirectUrl(true);
            if ($url) {
                $this->getResponse()->setRedirect($url);
            } else {
                $this->_redirectReferer(Mage::helper('checkout/cart')->getCartUrl());
            }
        } catch (Exception $e) {
            $this->_getSession()->addException($e, $this->__('Cannot add the item to shopping cart.'));
            Mage::logException($e);
            $this->_goBack();
        }
    }

    public function giftcardPostAction()
    {
        if (!Mage::helper('customer')->isLoggedIn()) {
            Mage::getSingleton('customer/session')->addError("To redeem your gift card or to use your gift card balance you need to be logged in.");
			Mage::getSingleton('customer/session')->authenticate($this);
			return;
		}
    	$giftcardCode = (string) $this->getRequest()->getParam('giftcard_code');
        $giftcardCode = trim($giftcardCode);
		$cardData = Mage::getResourceModel('giftcards/card_collection');
		$cardData->getSelect()
                ->where('main_table.card_code = ?', $giftcardCode)
		        ->where('main_table.status = ?', 'A');
		foreach ($cardData->getItems() as $card) {
			$card->activateCardForCustomer(Mage::getSingleton('customer/session')->getCustomerId());
		}
    	if ($cardData->count() > 0) {
    		$this->_getSession()->addSuccess(
            	$this->__('Gift Card "%s" was applied.', Mage::helper('core')->escapeHtml($giftcardCode))
            );
            Mage::getSingleton('giftcards/session')->setActive('1');
    	} else {
    		$this->_getSession()->addError(
            	$this->__('Gift Card "%s" is not valid.', Mage::helper('core')->escapeHtml($giftcardCode))
            );
    	}
    	$this->_goBack();
    }

    public function giftcardActiveAction()
    {
        if (!Mage::helper('customer')->isLoggedIn()) {
            Mage::getSingleton('customer/session')->addError('To redeem your gift card or to use your gift card balance you need to be logged in.');
			Mage::getSingleton('customer/session')->authenticate($this);
			return;
		}
    	if ((string) $this->getRequest()->getParam('giftcard_use')=='1'){
    		Mage::getSingleton('giftcards/session')->setActive('1');
    	} else {
    		Mage::getSingleton('giftcards/session')->setActive('0');
    	}
    	$this->_goBack();
    }
}
