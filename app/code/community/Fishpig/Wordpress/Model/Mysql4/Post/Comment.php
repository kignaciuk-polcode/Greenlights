<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Model_Mysql4_Post_Comment extends Fishpig_Wordpress_Model_Mysql4_Abstract
{
	public function _construct()
	{
		$this->_init('wordpress/post_comment', 'comment_ID');
	}

	/**
	 * Add data to the comment that has not been set
	 *
	 * @param Mage_Core_Model_Abstract $object
	 */
	protected function _beforeSave(Mage_Core_Model_Abstract $object)
	{
		if (!$object->getCommentContent()) {
			throw new Exception('No content was set for the comment');
		}
		
		$session = Mage::getSingleton('customer/session');
		$user = Mage::getModel('wordpress/user');
		
		if ($session->isLoggedIn()) {
			$customer = $session->getCustomer();
			
			if (!$object->getCommentAuthor()) {
				$object->setCommentAuthor($customer->getName());
			}
			
			if (!$object->getCommentAuthorEmail()) {
				$object->setCommentAuthorEmail($customer->getEmail());
			}

			if ($user->loadCurrentLoggedInUser()) {
				$object->setUserId($user->getId());
			}
		}
		elseif (Mage::helper('wordpress')->getCachedWpOption('comment_registration')) {
			throw new Exception('You must be logged in to comment on this post');
		}

		// Comment MUST have NAME & EMAIL
		if (Mage::helper('wordpress')->getCachedWpOption('require_name_email')) {
			if (!$object->getCommentAuthor() || !$object->getCommentAuthorEmail()) {
				throw new Exception('Comment author name and email must be set');
			}
		}
		
		/*
		 * Set comment dates
		 */
		if (!$object->hasCommentDate()) {
			$object->setCommentDate(now());
		}
		
		if (!$object->hasCommentDateGmt()) {
			$object->setCommentDateGmt(gmdate('Y-m-d H:i:s'));
		}
		
		/**
		 * Set comment Author IP
		 */
		if (!$object->hasData('comment_author_IP')) {
			$object->setData('comment_author_IP', $_SERVER['REMOTE_ADDR']);
		}
		
		/**
		 * Set comment status
		 */
		if (Mage::helper('wordpress')->getCachedWpOption('comment_moderation')) {
			// Before a comment appears An administrator must always approve the comment
			$object->setCommentApproved(0);
		}
		else if (Mage::helper('wordpress')->getCachedWpOption('comment_whitelist')) {
			// Comment author must have a previously approved comment 
			if ($user->getId()) {
				$select = $this->_getReadAdapter()
					->select()
					->from(Mage::helper('wordpress')->getTableName('comments'), 'comment_ID')
					->where('user_id=?', $user->getId())
					->where('comment_approved=?', 1)
					->limit(1);
					
				if (!$this->_getReadAdapter()->fetchOne($select)) {
					// User has no comments
					$object->setCommentApproved(0);
				}
			}
			else {
				// User isn't logged in so can't have a previous comment
				$object->setCommentApproved(0);
			}
		}
		
		/**
		 * Check for max links
		 */
		$maxLinks = (int)Mage::helper('wordpress')->getCachedWpOption('comment_max_links');
		
		if ($maxLinks > 0) {
			$matchedLinks = preg_match_all( '/<a [^>]*href/i', $object->getCommentContent(), $output);

			if ($matchedLinks >= $maxLinks) {
				// Comment has too many links and is probably spam
				$object->setCommentApproved(0);
			}
		}
		
		return parent::_beforeSave($object);
	}
	
	/**
	 * Send notification email to administrator
	 *
	 * @param Mage_Core_Model_Abstract $object
	 */
	protected function _afterSave(Mage_Core_Model_Abstract $object)
	{
		if (Mage::helper('wordpress')->getCachedWpOption('comments_notify')) {
//			$object->sendNotificationEmail();
		}
		elseif (Mage::helper('wordpress')->getCachedWpOption('comment_whitelist') && $object->getCommentApproved() == '0') {
//			$object->sendNotificationEmail();
		}
		
		if (Mage::helper('wordpress')->getCachedWpOption('moderation_notify') && $object->getCommentApproved() == '0') {
			$object->sendModerationEmail();
		}
	}
}
