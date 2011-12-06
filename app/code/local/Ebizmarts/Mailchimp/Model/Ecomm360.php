<?php

class Ebizmarts_Mailchimp_Model_Ecomm360 extends Ebizmarts_Mailchimp_Model_MCAPI{

	protected $_info = array();
	protected $_auxPrice = 0;
	protected $_order;
	protected $_productsToSkip = array(Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE, Mage_Catalog_Model_Product_Type::TYPE_BUNDLE);

	public function _construct(){

		parent::_construct();
  		$this->_init('mailchimp/ecomm360');
	}

	public function registerMe(){

		if(Mage::helper('mailchimp')->isEcomm360Activated()){
			$thirty_days = time()+60*60*24*30;
	        if (isset($_REQUEST['mc_cid'])){
	            setcookie('ebiz_mc_pro_campaign_id',trim($_REQUEST['mc_cid']), $thirty_days);
	        }
	        if (isset($_REQUEST['mc_eid'])){
	            setcookie('ebiz_mc_pro_email_id',trim($_REQUEST['mc_eid']), $thirty_days);
	        }
		}
        return $this;
	}

	public function runEcomm360($order){

		if (isset($_COOKIE['ebiz_mc_pro_campaign_id'],$_COOKIE['ebiz_mc_pro_email_id']) && Mage::helper('mailchimp')->isEcomm360Activated()){
			$this->logSale($order);
		}
		return $this;
	}

	private function logSale($order){

		$this->_order = $order;
		$apikey = Mage::helper('mailchimp')->getApiKey();
		if(!$apikey){
			return false;
		}

		$this->MCAPI($apikey);

        $this->_info = array(
                'id' => $this->_order->getIncrementId(),
                'campaign_id'=>$_COOKIE['ebiz_mc_pro_campaign_id'],
                'email_id'=>$_COOKIE['ebiz_mc_pro_email_id'],
                'total'=>$this->_order->getSubtotal(),
                'shipping'=>$this->_order->getShippingAmount(),
                'tax'  =>$this->_order->getTaxAmount(),
                'store_id'=>$this->_order->getStoreId(),
                'store_name' => $this->_order->getStoreName(),
                'plugin_id'=>1215,
                'items'=>array()
                );

		$this->setItemstoSend();

 	   $res = $this->campaignEcommOrderAdd($this->_info);
		if ($this->errorCode){
			$this->setCode($this->errorCode);
			$this->setMessage($this->errorMessage);

			Mage::helper('mailchimp')->addException($this);
			return false;
		}

		if($res) $this->registerInfo();
		return $this;
    }

    private function setItemstoSend(){

    	 foreach ($this->_order->getAllItems() as $item) {
			$mcitem = array();
            $product = Mage::getSingleton('catalog/product')->load($item->getProductId());

			if(in_array($product->getTypeId(), $this->_productsToSkip) && $product->getPriceType() == 0){
				if($product->getTypeId() == Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE){
					$this->_auxPrice = $item->getPrice();
				}
				continue;
			}

			$mcitem['product_id'] = $product->getEntityId();
            $mcitem['product_name'] = $product->getName();

            $names = array();
            $cat_ids = $product->getCategoryIds();

            if (is_array($cat_ids) && count($cat_ids)>0){
                $category = Mage::getModel('catalog/category')->load($cat_ids[0]);
                $mcitem['category_id'] = $cat_ids[0];
                $names[] = $category->getName();
                while ($category->getParentId() && $category->getParentId()!=1){
                    $category = Mage::getModel('catalog/category')->load($category->getParentId());
                    $names[] = $category->getName();
                }
            }
        	$mcitem['category_name'] = (count($names))? implode(" - ",array_reverse($names)) : 'None';
            $mcitem['qty'] = $item->getQtyOrdered();
         	$mcitem['cost'] = ($this->_auxPrice > 0)? $this->_auxPrice : $item->getPrice();
            $this->_info['items'][] = $mcitem;
            $this->_auxPrice = 0;
		}

		return $this;
    }

    private function registerInfo(){

		$this->setStoreId($this->_info['store_id'])
			 ->setIncrementId(trim($this->_info['id']))
	         ->setOrderId($this->_order->getEntityId())
	         ->setCampaignId($this->_info['campaign_id'])
	      	 ->setSentTime(date("Y-m-d H:i:s",time()))
		     ->save();
	}
}
