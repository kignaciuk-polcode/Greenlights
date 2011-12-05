<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

abstract class Fishpig_Wordpress_Helper_Debug_Test_Abstract extends Fishpig_Wordpress_Helper_Abstract
{
	/**
	 * Test title
	 *
	 * @var string
	 */
	protected $_title = null;
	
	/**
	 * Class used when error occurs
	 *
	 * @var string
	 */
	protected $_errorClass = 'error-msg';
	
	/**
	 * Class used when no error occurs
	 *
	 * @var string
	 */
	protected $_successClass = 'success-msg';
	
	/**
	 * Server response when unknown error occurs
	 *
	 * @var string
	 */
	const _error_unknown = 'An error was reported during this test. Please enable logging and check /var/log/wordpress.log';
	
	/**
	 * Server response when a previous test was unsuccessfull
	 *
	 * @var string
	 */
	const _error_cannot_test = '';//'This test cannot be performed until all previous tests are successfull';
	
	/**
	 * Determine whether to display test in results
	 * Some tests are needed so remove
	 *
	 * @var bool
	 */
	protected $_canDisplay = true;
	
	/**
	 * Function contains test logic
	 *
	 * Must be present in all descendent classes
	 *
	 * @return bool
	 */
	abstract protected function _performTest();
	
	/**
	 * Wrapper for self::_performTest()
	 * Processes result of test and converts to object
	 *
	 * @return Varien_Object
	 */
	final public function performTest()
	{
		try {
			if ($this->_performTest()) {
				if ($this->_canDisplay) {
					return $this->_createResultObject($this->_title, ': )', $this->_successClass);
				}
				
				return false;
			}
			
			throw new Exception(self::_error_unknown);
		}
		catch (Exception $e) {
			$this->log($e->getMessage());
			return $this->_createResultObject($this->_title, $e->getMessage(), $this->_errorClass);
		}
	}
	
	/**
	 * Converts the result data into an object
	 *
	 * @param string $title
	 * @param string $response
	 * @param string $resultClass
	 * @return Varien_Object
	 */
	protected function _createResultObject($title, $response , $resultClass)
	{
		return new Varien_Object(array('title' => $title, 'response' => $response, 'result' => $resultClass));
	}
	
	public function removeIndexFromUrl($url)
	{
		$url = str_replace('/index.php', '', $url);
		return str_replace('index.php', '', $url);
	}
	
}