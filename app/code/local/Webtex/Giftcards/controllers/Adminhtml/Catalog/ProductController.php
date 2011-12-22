<?php
require_once 'Mage/Adminhtml/controllers/Catalog/ProductController.php';
class Webtex_Giftcards_Adminhtml_Catalog_ProductController extends Mage_Adminhtml_Catalog_ProductController
{
    /**
     * Save product action
     */
    public function saveAction()
    {
        $storeId        = $this->getRequest()->getParam('store');
        $redirectBack   = $this->getRequest()->getParam('back', false);
        $productId      = $this->getRequest()->getParam('id');
        $isEdit         = (int)($this->getRequest()->getParam('id') != null);

        $data = $this->getRequest()->getPost();
        if ($data) {
            if (!isset($data['product']['stock_data']['use_config_manage_stock'])) {
                $data['product']['stock_data']['use_config_manage_stock'] = 0;
            }
            $product = $this->_initProductSave();
            
            // Added for Webtex_Giftcards module
            if ($product->getTypeId() == 'giftcards') {
            	$product->setRequiredOptions('1');
            }
            // End

            try {
                $product->save();
                $productId = $product->getId();

                /**
                 * Do copying data to stores
                 */
                if (isset($data['copy_to_stores'])) {
                    foreach ($data['copy_to_stores'] as $storeTo=>$storeFrom) {
                        $newProduct = Mage::getModel('catalog/product')
                            ->setStoreId($storeFrom)
                            ->load($productId)
                            ->setStoreId($storeTo)
                            ->save();
                    }
                }

                Mage::getModel('catalogrule/rule')->applyAllRulesToProduct($productId);

                $this->_getSession()->addSuccess($this->__('The product has been saved.'));
            }
            catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError($e->getMessage())
                    ->setProductData($data);
                $redirectBack = true;
            }
            catch (Exception $e) {
                Mage::logException($e);
                $this->_getSession()->addError($e->getMessage());
                $redirectBack = true;
            }
        }

        if ($redirectBack) {
            $this->_redirect('*/*/edit', array(
                'id'    => $productId,
                '_current'=>true
            ));
        }
        else if($this->getRequest()->getParam('popup')) {
            $this->_redirect('*/*/created', array(
                '_current'   => true,
                'id'         => $productId,
                'edit'       => $isEdit
            ));
        }
        else {
            $this->_redirect('*/*/', array('store'=>$storeId));
        }
    }
}
