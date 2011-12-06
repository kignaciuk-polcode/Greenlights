<?php
class Ebizmarts_Mailchimp_Model_Collection
	extends Varien_Data_Collection
{

	protected $_emailCollection = Array();
        
	public function __construct()
	{
            $apikey = Mage::helper('mailchimp')->getApiKey();
            $url = "http://".substr($apikey, -3).".sts.mailchimp.com/1.0/ListVerifiedEmailAddresses";
            $params = array('apikey'=>$apikey,);
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url.'?'.http_build_query($params));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $result = curl_exec($ch);
            curl_close ($ch);
            
            $data = json_decode($result);
            //result must be in the following format
            //Array ( [Addresses] => Array ( [0] => rodrigod@ebizmarts.com [1] => info@ampcity.co.uk ) [RequestId] => c951b2a4-7cc9-11e0-95a7-35c0647356dc ) 
            if (isset($data->http_code)) {
                Mage::getSingleton('core/session')->addError('STS integration not set on mailchimp');
            }
            if (isset($data->email_addresses)){
                $this->_emailCollection = $data->email_addresses;
            }
            
            return parent::__construct();
	}
	public function load($printQuery = false, $logQuery = false)
	{
		if($this->isLoaded() || is_null($this->_emailCollection)){
			return $this;
		}
         $ext = Array();
        foreach ($this->_emailCollection as $row) {
            $item = new Varien_Object;
            $ext['type'] = 'email';
            $ext['emailadress'] = $row;
            $item->addData($ext);
            $this->addItem($item);
        }

        $this->_setIsLoaded();

		return $this;
	}
}