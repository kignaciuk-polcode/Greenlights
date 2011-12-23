<?php
class MW_RewardPoints_RewardpointsController extends Mage_Core_Controller_Front_Action
{
    const EMAIL_TO_SEMDER_TEMPLATE_XML_PATH 	= 'rewardpoints/send_reward_points/sender_template';
    const EMAIL_TO_RECIPIENT_TEMPLATE_XML_PATH 	= 'rewardpoints/send_reward_points/recipient_template';
    const XML_PATH_EMAIL_IDENTITY				= 'rewardpoints/send_reward_points/email_sender';
    /**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
    
    protected function _getHelper()
    {
    	return Mage::helper('rewardpoints');
    }
    
   	protected function _sendEmailTransaction($emailto, $name, $template, $data)
   	{
		$storeId = Mage::app()->getStore()->getId();  
   		$templateId = Mage::getStoreConfig($template,$storeId);
		   
		
		  $translate  = Mage::getSingleton('core/translate');
		  $translate->setTranslateInline(false);
		  
		  try{
			  Mage::getModel('core/email_template')
			      ->sendTransactional(
			      $templateId, 
			      Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY, $storeId), 
			      $emailto, 
			      $name, 
			      $data, 
			      $storeId);
			  $translate->setTranslateInline(true);
		  }catch(Exception $e){
		  		$this->_getSession()->addError($this->__("Email can not send"));
		  }
   	}
   	
   	
    /**
     * Action predispatch
     *
     * Check customer authentication for some actions
     */
    public function preDispatch()
    {
        // a brute-force protection here would be nice

        parent::preDispatch();

        if (!$this->getRequest()->isDispatched()) {
            return;
        }

        $action = $this->getRequest()->getActionName();
        if (!preg_match('/^(create|login|logoutSuccess|forgotpassword|forgotpasswordpost|confirm|confirmation)/i', $action)) {
            if (!$this->_getSession()->authenticate($this)) {
                $this->setFlag('', 'no-dispatch', true);
            }
        } else {
            $this->_getSession()->setNoReferer(true);
        }
    }
	public function indexAction()
	{
		if(!(Mage::helper('rewardpoints')->moduleEnabled()))
		{
			$this->norouteAction();
			return;
		}
		//Check invition information if exist add reward point to friend
        $friend_id = Mage::getModel('core/cookie')->get('friend');
		$customer_id = $this->_getSession()->getCustomer()->getId();
		$collection_customer = Mage::getModel('rewardpoints/customer')->getCollection()
										->addFieldToFilter('customer_id', $customer_id);
		if(sizeof($collection_customer) == 0){
			$_customer = Mage::getModel('rewardpoints/customer')->getCollection();
			$write = Mage::getSingleton('core/resource')->getConnection('core_write');
	        $sql = 'INSERT INTO '.$_customer->getTable('customer').'(customer_id,mw_reward_point,mw_friend_id) VALUES('.$customer_id.',0,'. (($friend_id && Mage::helper('rewardpoints')->getInvitationModule())?$friend_id:0).')';
	        $write->query($sql);
		}
		
		$this->loadLayout();
		$this->_initLayoutMessages('customer/session');
		$this->_initLayoutMessages('checkout/session');
		$this->getLayout()->getBlock('head')->setTitle($this->__('Reward Points Management'));
		Mage::dispatchEvent('rewardpoints_manager_index',array(
           'model'    => $this->_getSession()->getCustomer()
        ));
		$this->renderLayout();
	}
/*	public function historyAction()
	{
		if(!(Mage::helper('rewardpoints')->moduleEnabled()))
		{
			$this->norouteAction();
			return;
		}
		Mage::register('title','Transaction History Manager');
		$this->loadLayout()->renderLayout();
	}*/
	
	public function sendAction()
	{
		if(!(Mage::helper('rewardpoints')->moduleEnabled()))
		{
			$this->norouteAction();
			return;
		}
		
		if(!Mage::helper('rewardpoints')->allowSendRewardPointsToFriend()){
			$this->norouteAction();
			return;
		}
		$this->_initLayoutMessages('customer/session');
		$this->_initLayoutMessages('checkout/session');
		
		if($this->getRequest()->getPost()){
			if($this->_getHelper()->enabledCapcha()){
				$require = dirname(dirname(__FILE__))."/Helper/Capcha/Securimage.php";
				require($require);
				  $img = new Securimage();
				  $valid = $img->check($this->getRequest()->getPost("code"));
			}else{
				$valid = true;
			}
			  if($valid)
			  {
				  	$_customer = Mage::getModel('rewardpoints/customer')->load($this->_getSession()->getCustomer()->getId());	//current customer
				  	$point = $this->getRequest()->getPost("amount");
				  	if($point < 0 ) $point = -$point;
				  	if($_customer->getMwRewardPoint() >= $point)
				  	{
					  	//send reward point
					  	$store_id = Mage::helper('core')->getStoreId();
					  	$website_id = Mage::getModel('core/store')->load($store_id)->getWebsiteId();
					  	$customer = Mage::getModel('customer/customer')->setWebsiteId($website_id)->loadByEmail($this->getRequest()->getPost("email"));
					  	if($customer->getId()!=$_customer->getId())
						{
							if($customer->getId()){
								//Add reward points to friend 
								$mwCustomer = Mage::getModel('rewardpoints/customer')->load($customer->getId());
								$mwCustomer->addRewardPoint($point);
								$historyData = array('type_of_transaction'=>MW_RewardPoints_Model_Type::RECIVE_FROM_FRIEND, 'amount'=>$point,'balance'=>$mwCustomer->getMwRewardPoint(), 'transaction_detail'=>$_customer->getId(), 'transaction_time'=>now(), 'status'=>MW_RewardPoints_Model_Status::COMPLETE);
								$mwCustomer->saveTransactionHistory($historyData);
								
								//Subtract reward points of current customer
								$_customer->addRewardPoint(-$point);
								$historyData = array('type_of_transaction'=>MW_RewardPoints_Model_Type::SEND_TO_FRIEND, 'amount'=>$point,'balance'=>$_customer->getMwRewardPoint() , 'transaction_detail'=>$customer->getId(), 'transaction_time'=>now(), 'status'=>MW_RewardPoints_Model_Status::COMPLETE);
								$_customer->saveTransactionHistory($historyData);
								
								$this->_getSession()->addSuccess($this->__("Your reward points were sent successfuly"));
								$this->_redirect('rewardpoints/rewardpoints/index');
							}else{
									//Subtract reward points of current customer
									$_customer->addRewardPoint(-$point);
									$historyData = array('type_of_transaction'=>MW_RewardPoints_Model_Type::SEND_TO_FRIEND, 'amount'=>$point, 'balance'=>$_customer->getMwRewardPoint(), 'transaction_detail'=>$this->getRequest()->getPost("email"), 'transaction_time'=>now(), 'status'=>MW_RewardPoints_Model_Status::PENDING);
									$_customer->saveTransactionHistory($historyData);
									//customer dose not exist
									$this->_getSession()->addSuccess($this->__("Your reward points were sent successfully"));
							}
							
							if(Mage::getStoreConfig('rewardpoints/send_reward_points/enable_send_email_to_recipient'))
							{
								//Send mail to frend
								$mailto = $this->getRequest()->getPost('email');
								$name = $this->getRequest()->getPost('name');
								$template = self::EMAIL_TO_RECIPIENT_TEMPLATE_XML_PATH;
								$postObject = new Varien_Object();
								$postObject->setData($this->getRequest()->getPost());
								$postObject->setSender($_customer->getCustomerModel());
								$postObject->setData('login_link',Mage::getUrl('customer/account/login'));
								$postObject->setData('register_link',Mage::getUrl('customer/account/create'));
								$this->_sendEmailTransaction($mailto, $name, $template, $postObject->getData());
							}
							
							if(Mage::getStoreConfig('rewardpoints/send_reward_points/enable_send_email_to_sender'))
							{
								//Send mail to sender
								$mailto = $_customer->getCustomerModel()->getEmail();
								$name = $_customer->getCustomerModel()->getName();
								$template = self::EMAIL_TO_SEMDER_TEMPLATE_XML_PATH;
								$postObject = new Varien_Object();
								$postObject->setData('amount',$this->getRequest()->getPost('amount'));
								$postObject->setData('name',$name);
								$this->_sendEmailTransaction($mailto, $name, $template, $postObject->getData());
							}
						}else
						{
							$this->_getSession()->addError($this->__("You can not send reward points to yourself"));
						}
				  	}else{
				  		//Current total reward points do not enought to send
				  		$this->_getSession()->addError($this->__("You do not have enough points to send to your friend"));
				  	}
			  }else{
			  	//return error
			  	$this->_getSession()->addError($this->__("Your security code is incorrect"));
			  }
		}else{
			$this->_getSession()->addError($this->__("You do not have permission!"));
		}
		$this->_redirect('rewardpoints/rewardpoints/index');
	}
	
	public function exchangeAction()
	{
		if(!(Mage::helper('rewardpoints')->moduleEnabled()))
		{
			$this->norouteAction();
			return;
		}
		
		if(!Mage::helper('rewardpoints')->allowExchangePointToCredit()){
			$this->norouteAction();
			return;
		}
		$points = $this->getRequest()->getPost('exchange_points');
		$_customer = Mage::getModel('rewardpoints/customer')->load($this->_getSession()->getCustomerId());
		if($points > $_customer->getRewardPoint())
		{
			$this->_getSession()->addError($this->__("You do not enought points to exchange"));
			return;
		}
		
		if(Mage::helper('rewardpoints')->getCreditModule()){
			$exchangeRate = explode('/',Mage::getStoreConfig('rewardpoints/exchange_to_credit/point_credit_rate'));
			if(sizeof($exchangeRate)==2)
			{
				if($points < 0) $points = -$points;
				$credit = ($points * $exchangeRate[1] * 1.0)/$exchangeRate[0];
				//add credit to customer
				$customerCredit = Mage::getSingleton('credit/creditcustomer')->load($this->_getSession()->getCustomer()->getId());
				$oldCredit = $customerCredit->getCredit();
				$newCredit = $oldCredit + $credit;
				$customerCredit->setCredit($newCredit)->save();
				$historyData = array('type_transaction'=>MW_Credit_Model_TransactionType::SEND_TO_FRIEND, 
	            					     'transaction_detail'=>$points, 
	            						 'amount'=>$credit, 
	            						 'beginning_transaction'=>$oldCredit,
	            						 'end_transaction'=>$newCredit,
	            					     'created_time'=>now());
	            Mage::getModel("credit/credithistory")->saveTransactionHistory($historyData);
				//Subtract points
				
				$customerRewardPoints = Mage::getModel('rewardpoints/customer')->load($this->_getSession()->getCustomer()->getId());
				$customerRewardPoints->addRewardPoint(-$points);
				$historyData = array('type_of_transaction'=>MW_RewardPoints_Model_Type::EXCHANGE_TO_CREDIT, 'amount'=>$points, 'balance'=>$customerRewardPoints->getMwRewardPoint(), 'transaction_detail'=>$credit, 'transaction_time'=>now(), 'status'=>MW_RewardPoints_Model_Status::COMPLETE);
            	$customerRewardPoints->saveTransactionHistory($historyData);
				
				$this->_getSession()->addSuccess($this->__("Your reward points was exchanged to credit successfuly"));
			}else{
				$this->_getSession()->addError($this->__("There is a system error. Please contact to administrator."));
			}
		}else{
			$this->_getSession()->addError($this->__("Credit module error or has not been installed yet"));
		}
		$this->_redirect('rewardpoints/rewardpoints/index');
	}
	
	public function imageAction()
	{
		//require(str_replace("index.php/","",Mage::getBaseDir()).DS.'mw_capcha'.DS.'Securimage.php');
		if(!Mage::helper('rewardpoints')->enabledCapcha()){
			$this->norouteAction();
			return;
		}
		$require = dirname(dirname(__FILE__))."/Helper/Capcha/Securimage.php";
		require($require);
		$hp = $this->_getHelper();
		$img = new Securimage();
		
		//Change some settings
		$img->use_wordlist = $hp->capchaUseWordList();
		$img->image_width = $hp->getCapchaImageWidth();
		$img->image_height = $hp->getCapchaImageHeight();
		$img->perturbation =$hp->getCapchaPerturbation();
		$img->code_length = $hp->getCapchaCodeLength();
		$img->image_bg_color = new Securimage_Color($hp->getCapchaBackgroundColor());
		$img->use_transparent_text = $hp->capchaUseTransparentText();
		$img->text_transparency_percentage = $hp->getCapchaTextTransparencyPercentage(); // 100 = completely transparent
		$img->num_lines = $hp->getCapchaNumberLine();
		$img->text_color = new Securimage_Color($hp->getCapchaTextColor());
		$img->line_color = new Securimage_Color($hp->getCapchaLineColor());
		$backgroundFile = $hp->getCapchaBackgroundImage();
		$img->show($backgroundFile);
	}	
}