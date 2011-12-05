<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Adminhtml_RedirectController extends Mage_Adminhtml_Controller_Action
{
	public function indexAction()
	{
		$ahelper = Mage::helper('wordpress/adminhtml');
		
		try {
			return $this->_loginToWordpressAdmin();
		}
		catch (Exception $e) {
			if (!Mage::helper('wordpress/db')->isConnected()) {
				return $ahelper->redirectToWordpressConfig();
			}
			else {
				if ($ahelper->getAutoLogin()) {
					$ahelper->addError('There was an error logging you into WordPress. Please check your WordPress Admin credentials below and try again')
						->addNotice('If you are sure that the details entered below are your correct WP-Admin login details you should check consult the WordPress log file for more information');
				}
				else {
					$ahelper->addError('Please set your Wordpress Admin area login details below. Once you have done this you will be able to login to Wordpress with 1 click by selecting Wordpress Admin from the top menu.');
				}
			
				$this->_redirect('wp_admin/adminhtml_wpAdmin/login', array('redirect_to' => base64_encode($this->_getDestinationPage())));
				return;
			}
		}
	}
	
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
			$curl = Mage::helper('wordpress/curl')
				->post($postUrl, $postData, array('header' => 1, 'follow_location' => 0, 'return_transfer' => 1), true);
				
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

		throw new Exception('There appears to be an error connecting to your WordPress database.');
	}

	protected function _getUsername()
	{
		return Mage::helper('wordpress/adminhtml')->getAutoLogin()->getUsername();
	}
	
	protected function _getPassword()
	{
		return Mage::helper('wordpress/adminhtml')->getAutoLogin()->getPassword();
	}	
}
