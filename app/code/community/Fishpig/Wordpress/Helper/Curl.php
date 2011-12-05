<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Helper_Curl extends Fishpig_Wordpress_Helper_Abstract
{
	/**
	 * Send a HTTP Post request
	 *
	 * @param string $postUrl
	 * @param array $postData
	 * @param array $params = array()
	 * @param bool $throwException
	 * @return Varien_Object
	 */
	public function post($postUrl, array $postData, array $params = array(), $throwException = false)
	{
		try {
			return $this->_post($postUrl, $postData, $params);
		}
		catch (Exception $e) {
			$this->log($e->getMessage());
			
			if ($throwException) {
				throw new Exception($e->getMessage());
			}		
		}
	}

	/**
	 * Send a HTTP Post request
	 *
	 * @param string $url
	 * @param array $postData
	 * @param array $params = array()
	 * @return Varien_Object
	 */
	protected function _post($postUrl, array $postData, array $params = array())
	{
		$postString = '';
		$params = new Varien_Object($params);
		
		foreach($postData as $field => $value) {
			$postString .= urlencode($field) .'='.urlencode($value).'&';
		}

		$postString = rtrim($postString, '&=?');

		$ch 		= 	curl_init();
						curl_setopt($ch,CURLOPT_URL, $postUrl);
						curl_setopt($ch,CURLOPT_POST, count($postData));
						curl_setopt($ch,CURLOPT_POSTFIELDS, $postString);
							
		if ($params->getHeader()) {
			curl_setopt($ch, CURLOPT_HEADER, 1);
		}
		
		if ($params->getFollowLocation()) {
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		}
		
		if ($params->getReturnTransfer()) {
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		}
		
		$result = curl_exec($ch);
		
		if (curl_errno($ch)) {
			throw new Exception('Curl error: ' . curl_error($ch));
		}
		
		$ch		= curl_close($ch);

		return new Varien_Object(array('channel' => $ch, 'result' => $result));
	}
	
}
