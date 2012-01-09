<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Adminhtml_RedirectController extends Fishpig_Wordpress_Controller_Adminhtml_Abstract
{
	/**
	 * Attempt to login to the WordPress Admin action
	 *
	 */
	public function indexAction()
	{
		try {
			return $this->_loginToWordpressAdmin();
		}
		catch (Exception $e) {
			if (!Mage::helper('wordpress/db')->isConnected() || !Mage::helper('wordpress/db')->isQueryable()) {
				return $this->redirectToWordpressConfig();
			}
			else {
				if ($this->getAutoLogin()) {
					$this->addError('There was an error logging you into WordPress. Please check your WordPress Admin credentials below and try again')
						->addNotice('If you are sure that the details entered below are your correct WP-Admin login details you should check consult the WordPress log file for more information');
				}
				else {
					$this->addError('Please set your Wordpress Admin area login details below. Once you have done this you will be able to login to Wordpress with 1 click by selecting Wordpress Admin from the top menu.');
				}
			
				$this->_redirect('wp_admin/adminhtml_wpAdmin/login', array('redirect_to' => base64_encode($this->_getDestinationPage())));
				return;
			}
		}
	}
	
	/**
	 * Attemp to login to WordPress Admin
	 *
	 */
	protected function _loginToWordpressAdmin()
	{
		$postUrl = Mage::helper('wordpress')->getBaseUrl('wp-login.php');
		$postData	= array(
			'log' => $this->_getUsername(),
			'pwd'	 => $this->_getPassword(),
			'rememberme' => 'forever',
			'wp-submit' => 'Log In',
			'redirect_to' => $this->_getDestinationPage(),
			'testcookie' => 1
		);
								
		try {
			$curl = $this->post($postUrl, $postData, array('header' => 1, 'follow_location' => 0, 'return_transfer' => 1), true);
				
			$result = $curl->getResult();

			if (strpos($result, 'Location: ') === false) {
				throw new Exception('Invalid response returned. Are the WP-Admin login details correct?');
			}

			foreach(explode("\n", $result) as $line) {
				$line = trim($line);
				
				if (substr($line, 0, 1) == '<') {
					break;
				}
				
				if ($line) {
					header($line, false);
				}
			}	
		}
		catch (Exception $e) {
			Mage::helper('wordpress')->log('RedirectController._loginToWordpress: '.$e->getMessage());
			throw $e;
		}
		
		$this->_redirectUrl($this->_getDestinationPage());
		return;
	}
	
	/**
	 * Retrieve the destination page
	 *
	 * @return string
	 */
	protected function _getDestinationPage()
	{
		$page = array();
		$page['default'] = 'index.php';
		$page['default'] = '';
		$page['posts'] = 'edit.php';
		$page['media'] = 'upload.php';
		$page['links'] = 'link-manager.php';	
		$page['pages'] = 'edit.php?post_type=page';
		$page['comments'] = 'edit-comments.php';
		$page['appearance'] = 'themes.php';
		$page['plugins'] = 'plugins.php';
		$page['users'] = 'users.php';
		$page['tools'] = 'tools.php';
		$page['settings'] = 'options-' . $this->getRequest()->getParam('wp_page_option', 'general') . '.php';

		$key = $this->getRequest()->getParam('wp_page', 'default');
		
		if (!isset($page[$key])) {
			$key = 'default';
		}
		
		$url = Mage::helper('wordpress')->getAdminUrl($page[$key]);
		
		if (substr($url, 0, strlen('/wp-admin/')) != '/wp-admin/') {
			return $url;
		}
		
		Mage::getSingleton('adminhtml/session')->addError(Mage::helper('wordpress')->__('Unable to connect to your WordPress database.'));
	}

	/**
	 * Retrieve the WordPress Admin username
	 *
	 * @return string
	 */
	protected function _getUsername()
	{
		return $this->getAutoLogin()->getUsername();
	}

	/**
	 * Retrieve the WordPress Admin password
	 *
	 * @return string
	 */	
	protected function _getPassword()
	{
		return $this->getAutoLogin()->getPassword();
	}	
	
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
			Mage::helper('wordpress')->log($e->getMessage());
			
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
