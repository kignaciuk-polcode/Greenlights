<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Model_Mysql4_Post_Category extends Fishpig_Wordpress_Model_Mysql4_Category_Abstract
{
	public function _construct()
	{
		$this->_init('wordpress/post_category', 'term_id');
	}

}
