<?php
class MW_Invitation_InvitationController extends Mage_Core_Controller_Front_Action
{
	const EMAIL_TO_RECIPIENT_TEMPLATE_XML_PATH 	= 'invitation/config/email_template';
    const XML_PATH_EMAIL_IDENTITY				= 'invitation/config/email_sender';
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
    
	/**
     * Retrieve customer session model object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('customer/session');
    }
    
    protected function getStringBetween($string, $startStr, $endStr)
    {
    	$startStrIndex = strpos($string,$startStr);
    	if($startStrIndex === false) return false;
    	$startStrIndex ++;
    	$endStrIndex = strpos($string,$endStr,$startStrIndex);
    	if($endStrIndex === false) return false;
    	return substr($string,$startStrIndex,$endStrIndex-$startStrIndex);
    }
    
   	protected function _sendEmailTransaction($emailto, $name, $template, $data)
   	{
		$storeId = Mage::app()->getStore()->getId();  
   		$templateId = Mage::getStoreConfig($template,$storeId);
		$customer = $this->_getSession()->getCustomer();
		  $translate  = Mage::getSingleton('core/translate');
		  $translate->setTranslateInline(false);
		  $sender = Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY, $storeId);
		  
		  if(Mage::getStoreConfig('invitation/config/using_customer_email'))
		  	$sender = array('name'=>$customer->getName(),'email'=>$customer->getEmail());
		  try{
			  Mage::getModel('core/email_template')
			      ->sendTransactional(
			      $templateId, 
			      $sender, 
			      $emailto, 
			      $name, 
			      $data, 
			      $storeId);
			  $translate->setTranslateInline(true);
		  }catch(Exception $e){
		  		$this->_getSession()->addError($this->__("Email can not send !"));
		  }
   	}
   	
	public function indexAction()
    {
    	$this->loadLayout();
		$this->_initLayoutMessages('customer/session');
		$this->_initLayoutMessages('checkout/session');
		$this->renderLayout();
    }
    
    public function inviteAction()
    {
    	$post = $this->getRequest()->getPost('email');
    	$post = trim($post," ,");
    	$emails = explode(',',$post);
    	
    	$validator = new Zend_Validate_EmailAddress();
    	$error = array();
    	foreach($emails as $email){
    		$name = $email;
    		$_name = $this->getStringBetween($email,'"','"');
    		$_email = $this->getStringBetween($email,'<','>');
    		
    		if($_email!== false && $_name !== false) 
    		{
    			$email = $_email;
    			$name = $_name;
    		}else if($_email!== false && $_name === false)
    		{
    			if(strpos($email,'"')===false)
    			{
    				$email = $_email;
    				$name = $email;
    			}
    		}
    		$email = trim($email);
	    	if($validator->isValid($email)) {
	    		// Send email to friend
				$template = self::EMAIL_TO_RECIPIENT_TEMPLATE_XML_PATH;
				$postObject = new Varien_Object();
				$customer = $this->_getSession()->getCustomer();
				$postObject->setSender($customer);
				$postObject->setMessage($this->getRequest()->getPost('message'));
				$postObject->setData('invitation_link',Mage::helper('invitation')->getLink($customer));
				$this->_sendEmailTransaction($email, $name, $template, $postObject->getData());
			}
			else {
			   $error[] = $email;
			}
    	}
    	if(sizeof($error))
    	{
	    	$err = implode("<br>",$error);
	    	$this->_getSession()->addError($this->__("These emails are invalid, the invitation message will not be sent to:<br>%s",$err));
    	}
		$msg = "Your email was sent success";
		if(sizeof($emails) >1) $msg = "Your Emails were sent successfully";
		if(sizeof($emails) > sizeof($error)) $this->_getSession()->addSuccess($this->__($msg));
    	$this->_redirect('invitation/invitation/index');
    }
    
    public function widgetAction()
    {
    	$body = '<html><head><script type="text/javascript" src="https://www.plaxo.com/ab_chooser/abc_comm.jsdyn"></script></head><body></body></html>';
    	$this->getResponse()->setBody($body);
    }
}
