<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Helper_Debug_Test extends Fishpig_Wordpress_Helper_Abstract
{
	/**
	 * Cache for the test results collection
	 *
	 * @var null|Varien_Data_Collection
	 */
	protected $_resultCollection = null;
	
	/**
	 * Gets the test results and stores them in the cache
	 *
	 * @return Varien_Data_Collection
	 */
	public function performIntegrationTests()
	{
		if (!$this->hasResults()) {
			$results = Mage::helper('wordpress/debug_test_resultCollection');

			foreach($this->getAvailableTestClasses() as $test) {
				if ($debugTest = Mage::helper('wordpress/debug_test_'.$test)->performTest()) {
					$results->addItem($debugTest);
				}
			}
			
			$this->_resultCollection = $results;
		}

		return $this->getResultCollection();
	}

	public function hasError()
	{
		$hasError = false;
		
		$results = $this->performIntegrationTests();
		
		foreach($results as $result) {
			if ($result->getResult() != 'success-msg') {
				$hasError = true;
			}
		}
		
		return $hasError;
	}
	
	/**
	 * Returns true if the results collection contains any results
	 *
	 * @return bool
	 */
	public function hasResults()
	{
		return ($this->_resultCollection != null);
	}
	
	/**
	 * Returns the results collection
	 *
	 * @return null|Varien_Data_Collection
	 */
	public function getResultCollection()
	{
		return $this->_resultCollection;
	}

	/**
	 * Returns the class names used to perform the tests
	 *
	 * @return array
	 */
	public function getAvailableTestClasses()
	{
		if ($this->isFullyIntegrated()) {
			return array('connection', 'query', 'wpUrls', 'blogRoute', 'homepage', 'path');
		}

		return array('connection', 'query');
	}	
}
