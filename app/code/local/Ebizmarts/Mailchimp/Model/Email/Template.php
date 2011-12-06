<?php

class Ebizmarts_Mailchimp_Model_Email_Template extends Mage_Core_Model_Email_Template{

    public function send($email, $name = null, array $variables = array()){

        if(!Mage::helper('mailchimp')->isStsActivated()){
            return parent::send($email, $name, $variables);
        }
        if (!$this->isValidForSend()) {
            Mage::logException(new Exception('This letter cannot be sent.')); // translation is intentionally omitted
            return false;
        }

        if (is_null($name)) {
            $name = substr($email, 0, strpos($email, '@'));
        }

        $variables['email'] = $email;
        $variables['name'] = $name;

        ini_set('SMTP', Mage::getStoreConfig('system/smtp/host'));
        ini_set('smtp_port', Mage::getStoreConfig('system/smtp/port'));

        $setReturnPath = Mage::getStoreConfig(self::XML_PATH_SENDING_SET_RETURN_PATH);
        switch ($setReturnPath) {
            case 1:
                $returnPathEmail = $this->getSenderEmail();
                break;
            case 2:
                $returnPathEmail = Mage::getStoreConfig(self::XML_PATH_SENDING_RETURN_PATH_EMAIL);
                break;
            default:
                $returnPathEmail = null;
                break;
        }
        $this->setUseAbsoluteLinks(true);
        $text = $this->getProcessedTemplate($variables, true);

        $to_names = array();
        array_push($to_names, $name);
        $to_emails = array();
        if (is_array($email)) {
                foreach ($email as $emailOne) {
                    array_push($to_emails, $emailOne);
                }
            } else {
                array_push($to_emails,$email);
            }

        $apikey = Mage::helper('mailchimp')->getApiKey();
        $url = "http://".substr($apikey, -3).".sts.mailchimp.com/1.0/SendEmail";
        $message = array (
					        'html'=>$text,
					        'text'=>$text,
					        'subject'=>'=?utf-8?B?'.base64_encode($this->getProcessedTemplateSubject($variables)).'?=',
					        'from_name'=>$this->getSenderName(),
					        'from_email'=>$this->getSenderEmail(),
					        'to_email'=>$to_emails,
					        'to_name'=>$to_names
				        );

		$params = array(
							'apikey'=>$apikey,
							'message'=>$message,
							'track_opens'=>true,
							'track_clicks'=>false,
							'tags'=>''
						);

		$ch = curl_init();

		/*****this code has been updated thanks to manisanjai*****************************/
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
		/*****this code has been updated thanks to manisanjai*****************************/

		$result = curl_exec($ch);
		curl_close ($ch);
		$data = json_decode($result);

		if (isset($data->message_id)) {
		     Mage::getSingleton('adminhtml/session')->addSuccess('e-mail successfully sent.');
		}else {
		 	Mage::getSingleton('adminhtml/session')->addError('There was an error sending your email, internal code error: '.$data->aws_code);
		 	return false;
		}
		return true;
    }

}
