<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_IndexController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
	{
		$this->_redirect('*/homepage/index');
	}
}
