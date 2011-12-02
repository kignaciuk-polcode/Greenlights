<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Adminhtml_SupportController extends Fishpig_Wordpress_Controller_Adminhtml_Abstract
{
	public function indexAction()
	{
		$this->loadLayout();
		$this->renderLayout();
	}

	public function sendAction()
	{
		if ($data = $this->_prepareData()) {
			try {
				if ($this->_sendEmail($data) == false) {
					throw new Exception($this->__('There was an unknown error while sending your '. $this->getEnquiryType().'.'));
				}
				
				$this->_unsetSessionData();
				$this->getSession()->addSuccess($this->__('Your '.$this->getEnquiryType().' was sent successfully. Please allow 24 hours for a reply'));
				$this->_redirect('adminhtml/dashboard');
			}
			catch (Exception $e) {
				$this->_saveSessionData();
				Mage::helper('wordpress')->log($e->getMessage());
				$this->getSession()->addError($e->getMessage());
				$this->_redirect('wp_admin/adminhtml_support/', array('type' => $this->getEnquiryTypeId()));
			}
		}
	}
	
	protected function _sendEmail($data)
	{
		$email = new Zend_Mail();
		$email->addTo('help@fishpig.co.uk', 'FishPig')
			->setFrom($data->getSenderEmail(), $data->getSenderName())
			->setSubject($data->getSubject())
			->setBodyText($data->getBodyText());
			
		return $email->send();
	}
	
	protected function _prepareData()
	{
		if ($postData = $this->_getRawData()) {
			$doubleBreak = "\n\n\n\n";
			
			$body = $postData->getContent().$doubleBreak;
			$body .= "This message was sent using the support form at ".Mage::getBaseUrl().$doubleBreak;
			$body .= print_r(Mage::helper('wordpress/debug_environmentReport')->getReport(), true).$doubleBreak;
			$body .= Mage::helper('wordpress/debug_environmentReport')->getLogEntries(100).$doubleBreak;
			$postData->setBodyText($body);
			
			$postData->setSubject($postData->getEnquiryType().': '.$postData->getSubject());
		
			return $postData;
		}
		
		return false;
	}
	
	
	protected function _getRawData()
	{
		if ($post = $this->getRequest()->getPost()) {
			foreach($post as $index => $value) {
				$post[$index] = htmlspecialchars($value);
			}
			
			return new Varien_Object(array_merge(
				array(
					'sender_name' => 'Support Form',
					'sender_email' => 'help@fishpig.co.uk',
					'enquiry_type' => 'Support Ticket',
					'subject' => 'No Subject',
					'content' => 'Body not set',
				), $post));
		}
		
		return false;
	}

	protected function _saveSessionData()
	{
		if ($post = $this->getRequest()->getPost()) {
			$this->getSession()->setSupportData($post);
		}
	}
	
	protected function _unsetSessionData()
	{
		$this->getSession()->setSupportData(null);
	}
	
	public function getEnquiryType()
	{
		return ($type = $this->getRequest()->getPost('enquiry_type')) ? $type : $this->__('Support Ticket');
	}
	
	public function getEnquiryTypeId()
	{
		return ('Support Ticket' == $this->getRequest()->getPost('enquiry_type')) ? 0 : 1;
	
	}
}

