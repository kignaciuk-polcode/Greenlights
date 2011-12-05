<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_RobotController extends Fishpig_Wordpress_Controller_Abstract
{
	/**
	 * Display the blog robots.txt file
	 *
	 */
	public function indexAction()
	{
		$this->getResponse()->setHeader('Content-Type', 'text/plain;charset=utf8');
		
		$this->getResponse()->setBody(
			$this->getLayout()->createBlock('core/template')->setTemplate('wordpress/robots.phtml')->toHtml()
		);
	}
}
