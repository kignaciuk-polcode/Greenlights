<?php

class Fishpig_Wordpress_Helper_Db extends Fishpig_Wordpress_Helper_Abstract
{
	/**
	 * Map table names
	 *
	 * @param string $website = null
	 * @param string $store = null
	 */
	protected function _mapTables($website = null, $store = null)
	{
		$tables = array('commentmeta', 'comments', 'links', 'options', 'postmeta', 'posts', 'terms', 'term_relationships', 'term_taxonomy', 'usermeta', 'users');
	
		foreach($tables as $table) {
			Mage::getSingleton('core/resource')->setMappedTableName($table, $this->getTablePrefix($website, $store) . $table);
		}
		
		Mage::dispatchEvent('wordpress_database_map_tables', array('website' => $website, 'store' => $store));
	}
	
	/*
	 * Returns the table prefix used by Wordpress
	 *
	 * @return string
	 */
	public function getTablePrefix($website = null, $store = null)
	{
		if (!$this->_isCached('table_prefix')) {
			$this->_cache('table_prefix', $this->getCachedConfigValue('wordpress/database/table_prefix', $website, $store));
		}
		
		return $this->_cached('table_prefix');
	}
	
	/**
	 * Retrieve an entities table name
	 *
	 * @param string $table
	 * @return string
	 */
	public function getTableName($table)
	{
		return Mage::getSingleton('core/resource')->getTableName($table);
	}
	
	/**
	  * Returns true if it is possible to query the DB
	  *
	  * @param bool $graceful
	  * @return true
	  */
	public function isQueryable()
	{
		if (!$this->_isCached('is_queryable')) {
			$this->_cache('is_queryable', false);
			
			if ($this->isConnected()) {
				$adapter = $this->getReadAdapter();
				$select = $adapter->select()
					->from($this->getTableName('wordpress/post'), 'ID')
					->limit(1);
				
				try {
					$adapter->fetchOne($select);
					$this->_cache('is_queryable', true);
				}
				catch (Exception $e) {
					$this->log($e->getMessage());
				}
			}
		}
		
		return $this->_cached('is_queryable');
	}
	
	/**
	 * Retriev the read adapter
	 *
	 * @return false|Varien_Db_Adapter_Pdo_Mysql
	 */	
	public function getReadAdapter()
	{
		if ($this->isConnected()) {
			if ($this->isSameDatabase()) {
				return Mage::getSingleton('core/resource')->getConnection('core_read');
			}
		
			return $this->_getWordPressAdapter();
		}
		
		return false;
	}
	
	/**
	 * Retriev the write adapter
	 *
	 * @return false|Varien_Db_Adapter_Pdo_Mysql
	 */
	public function getWriteAdapter()
	{
		if ($this->isConnected()) {
			if ($this->isSameDatabase()) {
				return Mage::getSingleton('core/resource')->getConnection('core_write');
			}
		
			return $this->_getWordPressAdapter();
		}
		
		return false;
	}
	
	/**
	 * Retrieve the WordPress database adapter
	 *
	 * @return false|Varien_Db_Adapter_Pdo_Mysql
	 */
	protected function _getWordPressAdapter()
	{
		if ($this->isConnected()) {
			return Mage::getSingleton('core/resource')->getConnection('wordpress');
		}
		
		return false;
	}

	/**
	 * Determine whether the DB connection is active
	 *
	 * @return bool|null
	 */
	public function isConnected()
	{
		if (!$this->_isCached('db_connected')) {
			$this->_connect();
		}
		
		return $this->_cached('db_connected');
	}

	/**
	 * Connect to the WordPress database
	 *
	 * @param string $website = null
	 * @param string $store = null
	 * @return bool
	 */
	public function connect($website = null, $store = null)
	{
		if (!$this->_isCached('db_connected')) {
			$this->_connect($website, $store);
		}
		
		return $this->isConnected();
	}
	
	/**
	 * Connect to the database
	 *
	 * @param string $website = null
	 * @param string $store = null
	 * @return bool
	 */
	protected function _connect($website = null, $store = null)
	{
		$this->_cache('db_connected', false);
		
		list($website, $store) = $this->_ensureWebsiteAndStore($website, $store);
		
		$this->_mapTables($website, $store);
		
		if ($this->isSameDatabase()) {
			$this->_cache('db_connected', true);
		}
		else if ($configs = $this->_getDatabaseDetails($website, $store)) {
			try {
				$connection = Mage::getSingleton('core/resource')->createConnection('wordpress', 'pdo_mysql', $configs);
			
				if (!is_object($connection)) {
					/* Magento 1.3.2.4 */
					throw new Exception('Error connecting to the WordPress database');
				}
				
				$connection->getConnection();
	
				$this->_cache('db_connected', $connection->isConnected());			
			}
			catch (Exception $e) {
				$this->log($e->getMessage());
				$this->_cache('db_connected', false);
			}
		}

		return $this->_cached('db_connected');
	}
	
	/**
	 * Retrieve an array of the database connection details
	 *
	 * @param string $website = null
	 * @param string $store = null
	 * @return array|false
	 */
	protected function _getDatabaseDetails($website = null, $store = null)
	{
		$configs = array('model' => 'mysql4', 'active' => '1', 'host' => '', 'username' => '', 'password' => '', 'dbname' => '', 'charset' => 'utf8');
		
		foreach($configs as $key => $defaultValue) {
			if ($value = $this->getCachedConfigValue('wordpress/database/' . $key, $website, $store)) {
				$configs[$key] = $value;
			}
		}

		foreach(array('username', 'password', 'dbname') as $field) {
			if (isset($configs[$field])) {
				$configs[$field] = Mage::helper('core')->decrypt($configs[$field]);
			}
		}
		
		if (isset($configs['host']) && $configs['host']) {
			return $configs;
		}
		
		return false;
	}
}
