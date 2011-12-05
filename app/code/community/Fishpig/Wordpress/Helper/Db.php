<?php

class Fishpig_Wordpress_Helper_Db extends Fishpig_Wordpress_Helper_Abstract
{
	/*
	 * Stores mapped table names to stop remapping
	 *
	 * @var string
	 */
	protected $_mappedTableNames = array();

	/**
	 * Connection flag
	 *
	 * @var bool
	 */
	protected $_isConnected = null;
	
	/*
	 * Returns the given table name with the Wordpress prefix
	 * Maps the table name in Magento to ensure correct prefix used
	 *
	 * @param string $table
	 * @return string
	 */
	public function getTableName($table)
	{
		if (!in_array($table, $this->_mappedTableNames)) {
			Mage::getSingleton('core/resource')->setMappedTableName($table, $this->_getTableName($table));
			$this->_mappedTableNames[$this->_getTableName($table)] = $table;
		}
	
		return $this->_getTableName($table);
	}
	
	/*
	 * Returns the given table name and Wordpress table prefix
	 * This function doesn't map the table name
	 *
	 * @param string $table
	 * @return string
	 */
	protected function _getTableName($table = '')
	{
		return $this->getTablePrefix().$table;
	}
	
	/*
	 * Returns the table prefix used by Wordpress
	 *
	 * @return string
	 */
	public function getTablePrefix()
	{
		if (!isset($this->_cache['table_prefix'])) {
			$this->_cache['table_prefix'] = $this->getStoreConfig('wordpress/database/table_prefix');
		}
		
		return $this->_cache['table_prefix'];
	}
	
	/*
	 * Map the WordPress tables
	 */
	public function mapTables()
	{
		$tables = array('commentmeta', 'comments', 'links', 'options', 'posts', 'terms', 'term_relationships', 'term_taxonomy', 'usermeta', 'users');
		
		foreach($tables as $table) {
			Mage::helper('wordpress')->getTableName($table);
		}
	}
	
	/**
	  * Returns true if connected to DB
	  *
	  * @param bool $graceful
	  * @return bool
	  */
	public function isConnected($graceful = true)
	{
		if (is_null($this->_isConnected)) {
			$conn = $this->getWordpressRead();

			try {
				if (!is_object($conn)) {
					/* Magento .1.3.2.4 */
					throw new Exception('Error connecting to the WordPress database');
				}
				
				$conn->getConnection();

				$this->_isConnected = $conn->isConnected();
			}
			catch (Exception $e) {
				$this->log($e->getMessage());
				$this->_isConnected = false;
				
				if (!$graceful) {
					throw $e;
				}
			}
		}
		
		return $this->_isConnected;
	}

	/**
	  * Returns true if it is possible to query the DB
	  *
	  * @param bool $graceful
	  * @return true
	  */
	public function isQueryable($graceful = true)
	{
		if ($this->isConnected()) {
			$conn = $this->getWordpressRead();

			try {
				$conn->fetchRow('SELECT * FROM ' . $this->getTableName('posts') . ' LIMIT 1');
				return true;
			}
			catch (Exception $e) {
				$this->log('Debug.canQuery: '.$e->getMessage());
				
				if (!$graceful) {
					throw $e;
				}
			}
		}
		
		return false;
	}
	
	/**
	 * Returns the default read connection for the WordPress DB
	 *
	 * @return 
	 */
	public function getWordpressRead()
	{
		if (!$this->isSameDatabase()) {
			return Mage::getSingleton('core/resource')->getConnection('wordpress');
		}
		
		return Mage::getSingleton('core/resource')->getConnection('core_read');
	}
	
	public function hasBeenInitialised()
	{
		return Mage::registry('wordpress_db_init');
	}
}
