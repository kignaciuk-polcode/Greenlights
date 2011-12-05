<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Block_Post_View_Comment_Form extends Fishpig_Wordpress_Block_Post_View_Abstract
{
	public function __construct()
	{
		parent::__construct();
		$this->setTemplate('wordpress/post/view/comment/form.phtml');
	}
	
	/**
	 * Retrieve the comment form action
	 *
	 * @return string
	 */
	public function getCommentFormAction()
	{
		return $this->helper('wordpress')->getUrl('post-comment');
	}

	/**
	 * Determine whether the customer needs to login before commenting
	 *
	 * @return bool
	 */
	public function customerMustLogin()
	{
		if ($this->helper('wordpress')->getCachedWpOption('comment_registration')) {
			return !Mage::getSingleton('customer/session')->isLoggedIn();
		}
		
		return false;
	}
	
	/**
	 * Retrieve the link used to log the user in
	 * If redirect to dashboard after login is disabled, the user will be redirected back to the blog post
	 *
	 * @return string
	 */
	public function getLoginLink()
	{
		return Mage::getUrl('customer/account/login', array(
			'referer' => $this->helper('core')->urlEncode($this->getPost()->getPermalink() . '#respond'),
		));
	}
	
	/**
	 * Retrieve the HTML used to display the Recaptcha box
	 *
	 * @return string
	 */
	public function getRecaptchaHtml()
	{
		return $this->helper('wordpress/recaptcha')->getRecaptchaHtml();
	}
	
	/**
	 * Returns true if the user is logged in
	 *
	 * @return bool
	 */
	public function isCustomerLoggedIn()
	{
		return Mage::getSingleton('customer/session')->isLoggedIn();
	}
	
	/**
	 * Retrieve the post comment data stored in the session
	 *
	 * @return null|array
	 */
	public function getSessionData()
	{
		return Mage::getSingleton('wordpress/session')->getPostCommentData($this->getPost());
	}
}
