<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Helper_Debug_Test_ResultCollection extends Varien_Data_Collection
{
	/**
	 * Ensures that the collection size is correctly calculated
	 *
	 * @return int
	 */
	public function getSize()
	{
		return count($this->_items);
	}
}
