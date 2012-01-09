<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Model_Session extends Mage_Core_Model_Session_Abstract
{
	public function __construct()
	{
		$this->init('wordpress');
	}
	
	/**
	 * Stores a post's comment data in the session in case of error
	 *
	 * @param Fishpig_Wordpress_Model_Post $post
	 * @param string $author
	 * @param string $email 
	 * @param string $url
	 * @param string $comment
	 */
	public function setPostCommentData(Fishpig_Wordpress_Model_Post $post, $author, $email, $url, $comment)
	{
		$token = 'post_comment_data_' . $post->getId();
		$data = new Varien_Object(array(
			'author' => $author, 
			'email'	=> $email, 
			'url' => $url,
			'comment' => $comment
		));

		return parent::setData($token, $data);
	}
	
	/**
	 * Retrieve a post's comment data
	 *
	 * @param Fishpig_Wordpress_Model_Post $post
	 * @return Varien_Object
	 */
	public function getPostCommentData(Fishpig_Wordpress_Model_Post $post)
	{
		if ($post->getId()) {
			$obj = $this->getData('post_comment_data_' . $post->getId());
			
			if ($obj instanceof Varien_Object) {
				return $obj;
			}
		}
		
		return new Varien_Object();
	}
	
	/**
	 * Unset a post's comment data
	 *
	 * @param Fishpig_Wordpress_Model_Post $post
	 */
	public function removePostCommentData(Fishpig_Wordpress_Model_Post $post)
	{
		return $this->unsetData('post_comment_data_' . $post->getId());
	}
}
