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
}
