<?php

class Ebizmarts_Mailchimp_Adminhtml_StsController extends Mage_Adminhtml_Controller_Action{

	public function indexAction(){

        $this->loadLayout()
        	->_setActiveMenu('newsletter')
        	->_addContent($this->getLayout()->createBlock('mailchimp/adminhtml_sts'))
        	->renderLayout();
    }

    public function saveAction(){

        $apikey = Mage::helper('mailchimp')->getApiKey();
        $url = "http://".substr($apikey, -3).".sts.mailchimp.com/1.0/VerifyEmailAddress";
        $params = array('apikey'=>$apikey,'email'=>$this->getRequest()->getPost('emailaddress'));

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url.'?'.http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close ($ch);
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('mailchimp')->__('The e-mail address was successfully added, a confirmation email has been sent to this account.'));
        $this->_redirect('*/*/index');
	}

	public function newAction(){

		$this->loadLayout();
	    $this->_setActiveMenu('newsletter');

		$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

		$this->_addContent($this->getLayout()->createBlock('mailchimp/adminhtml_sts_edit'))
			 ->_addLeft($this->getLayout()->createBlock('mailchimp/adminhtml_sts_edit_tabs'));

		$this->renderLayout();
	}

    public function massremoveAction(){

        $apikey = Mage::helper('mailchimp')->getApiKey();
        $url = "http://".substr($apikey, -3).".sts.mailchimp.com/1.0/DeleteVerifiedEmailAddress";
		$cnt = 0;
        foreach ($this->getRequest()->getPost('emailadress') as $email) {
            $params = array('apikey'=>$apikey,'email'=>$email);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url.'?'.http_build_query($params));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close ($ch);
            $cnt++;
        }
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('mailchimp')->__(($cnt > 1)? 'The e-mails were successfuly deleted.' : 'The e-mail was successfuly deleted.'));
        $this->_redirectReferer();
    }

    public function removeAction(){

		$apikey = Mage::helper('mailchimp')->getApiKey();
        $url = "http://".substr($apikey, -3).".sts.mailchimp.com/1.0/DeleteVerifiedEmailAddress";
        $params = array('apikey'=>$apikey,'email'=>$this->getRequest()->getParam('emailadress'));
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url.'?'.http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
        curl_close ($ch);
        Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('mailchimp')->__('The e-mail was successfuly deleted.'));
        $this->_redirectReferer();
	}

    public function sendtestAction(){

        $to_emails = array($this->getRequest()->getParam('emailadress'));
        $apikey = Mage::helper('mailchimp')->getApiKey();
        $url = "http://".substr($apikey, -3).".sts.mailchimp.com/1.0/SendEmail";
        $message = array (
					    'html'=>'This is a test message. Powered by Ebizmarts MailChimp',
					    'text'=>'This is a test message. Powered by Ebizmarts MailChimp',
					    'subject'=>'Test Message From Ebizmarts MailChimp',
					    'from_name'=>'Test message',
					    'from_email'=>$this->getRequest()->getParam('emailadress'),
					    'to_email'=>$to_emails,
					    'to_name'=>$this->getRequest()->getParam('emailadress')
            			);
        $params = array(
					    'apikey'=>$apikey,
					    'message'=>$message,
					    'track_opens'=>true,
					    'track_clicks'=>false,
					    'tags'=>''
					    );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url.'?'.http_build_query($params));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $result = curl_exec($ch);
        curl_close ($ch);
        $data = json_decode($result);
        if (isset($data->message_id)) {
             Mage::getSingleton('adminhtml/session')->addSuccess('Test e-mail successfully sent.');
        }else {
         Mage::getSingleton('adminhtml/session')->addError('There was an error sending your email, internal code error: '.$data->aws_code);
        }
        $this->_redirectReferer();
    }

}