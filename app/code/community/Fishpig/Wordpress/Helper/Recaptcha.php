<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Helper_Recaptcha extends Fishpig_Wordpress_Helper_Abstract
{
	protected $_libraryIncluded = false;
	
	/**
	 * Include the Recaptcha library
	 *
	 * @return bool
	 */
	public function includeRecaptcha()
	{
		if (!$this->_libraryIncluded) {

			$file = Mage::getBaseDir('code') . DS . implode(DS, array('community', 'Fishpig', 'Wordpress', 'lib', 'recaptcha', 'recaptchalib.php'));
		
			if (file_exists($file)) {
				require_once($file);
				$this->_libraryIncluded = true;
				return true;
			}
		}
		
		return false;
	}
	
	/**
	 * Determine whether the given value is a valid value
	 *
	 * @param string $challenge
	 * @param string $response
	 * @param bool $graceful
	 * @return bool
	 */
	public function isValidValue($challenge, $response, $graceful = false)
	{
		if ($this->includeRecaptcha()) {
			$resp = recaptcha_check_answer($this->getRecaptchaPrivateKey(), $this->getRemoteAddress(), $challenge, $response);

			if (!$resp->is_valid) {
				$this->log('Captcha: ' . $resp->error);
				
				if (!$graceful) {
					throw new Exception($resp->error);
				}
				
				return false;
			}
			
			return true;
		}
		
		return false;
	}
	
	/**
	 * Retrieve the HTML for the recaptcha form
	 *
	 * @return string
	 */
	public function getRecaptchaHtml()
	{
		if ($this->includeRecaptcha()) {
			return $this->getRecaptchaThemeOptionsJs() 
				. recaptcha_get_html($this->getRecaptchaPublicKey());
		}
	}
	
	/**
	 * Retrieve the recaptcha public key
	 *
	 * @return string
	 */
	public function getRecaptchaPublicKey()
	{
		return Mage::getStoreConfig('wordpress_blog/recaptcha/public_key');
	}

	/**
	 * Retrieve the recaptcha private key
	 *
	 * @return string
	 */	
	public function getRecaptchaPrivateKey()
	{
		return Mage::getStoreConfig('wordpress_blog/recaptcha/private_key');
	}
	
	/**
	 * Generate the JS options for the Recaptcha box
	 * Allows you to change the theme and language
	 *
	 * @return string
	 */
	public function getRecaptchaThemeOptionsJs()
	{
		$options = array();
		$options['theme'] = Mage::getStoreConfig('wordpress_blog/recaptcha/theme');
		$options['lang'] = Mage::getStoreConfig('wordpress_blog/recaptcha/language');
		$js = '';
		
		foreach($options as $code => $value) {
			if ($value) {
				$js .= sprintf("%s : '%s', ", $code, $value);
			}
		}
		
		if ($js) {
			return sprintf('<script type="text/javascript">var RecaptchaOptions = {%s};</script>', rtrim($js, ", "));		
		}
	}
	
	/**
	 * Determine whether recaptcha is enabled
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		return Mage::getStoreConfig('wordpress_blog/recaptcha/enabled');
	}
	
	/**
	 * Retrieve the remote address
	 *
	 * @return string
	 */
	public function getRemoteAddress()
	{
		return $_SERVER['REMOTE_ADDR'];
	}
}
