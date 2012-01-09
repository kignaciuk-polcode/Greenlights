<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Helper_Search extends Fishpig_Wordpress_Helper_Abstract
{
	/**
	 * The query key used to store the search query
	 *
	 * @var string
	 */
	const QUERY_VAR_NAME = 's';
	
	/**
	 * Retrieve the query variable
	 *
	 * @return string
	 */
	public function getQueryVarName()
	{
		return self::QUERY_VAR_NAME;
	}
	
	/**
	 * Retrieve the raw query string text
	 *
	 * @return string|null
	 */
	public function getRawSearchString()
	{
		$str = trim(Mage::app()->getRequest()->getParam($this->getQueryVarName()));
		
		$str = urldecode($str ? $str : trim(Mage::app()->getRequest()->getParam('search')));
		
		return str_replace('-', ' ', $str);
	}

	/**
	 * Retrieve the escaped query string text
	 *
	 * @return string|null
	 */	
	public function getEscapedSearchString()
	{
		if (method_exists($this, 'htmlEscape')) {
			return $this->htmlEscape($this->getRawSearchString());
		}

		return $this->escapeHtml($this->getRawSearchString());
	}
	
	/**
	 * Retrieve a parsed version of the search string
	 * If search by single word, string will be split on each space
	 *
	 * @return array
	 */
	public function getParsedSearchString()
	{
		if ($this->isSearchByWords()) {
			$words = explode(' ', $this->getRawSearchString());
			$maxWords = $this->getMaxSearchWords();

			if (count($words) > $maxWords) {
				$words = array_slice($words, 0, $maxWords);
			}
		}
		else {
			$words = array($this->getRawSearchString());
		}
		
		$minWordLength = $this->getMinimumWordLength();

		foreach($words as $it => $word) {
			if (strlen($word) < $minWordLength) {
				unset($words[$it]);
			}
		}
		
		return $words;
	}
	
	/**
	 * Determine whether the search feature is enabled
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		return Mage::getStoreConfigFlag('wordpress_blog/search/enabled');
	}
	
	/**
	 * Retrieve the default search input value
	 *
	 * @return string
	 */
	public function getDefaultInputValue()
	{
		return Mage::getStoreConfig('wordpress_blog/search/default_input_value');
	}

	/**
	 * Retrieve the search logical operator
	 *
	 * @return string
	 */	
	public function getLogicalOperator()
	{
		return Mage::getStoreConfig('wordpress_blog/search/logical_operator');
	}
	
	/**
	 * Retrieve the searchable fields
	 *
	 * @return array
	 */
	public function getSearchableFields()
	{
		return explode(',', Mage::getStoreConfig('wordpress_blog/search/searchable_fields'));
	}
	
	/**
	 * Determine whether to search by single words or phrase
	 *
	 * @return bool
	 */
	public function isSearchByWords()
	{
		return Mage::getStoreConfigFlag('wordpress_blog/search/search_by_words');
	}
	
	/**
	 * Retrieve the maximum amount of single words that can be searched at a time
	 * Setting this value to high may cause some of the queries to become quite slow
	 *
	 * @return int
	 */
	public function getMaxSearchWords()
	{
		return (int)Mage::getStoreConfig('wordpress_blog/search/max_search_words');
	}
	
	/**
	 * Retrieve the minimum word length
	 * Setting this value below 3 may cause a lot of results
	 *
	 * @return int
	 */
	public function getMinimumWordLength()
	{
		return (int)Mage::getStoreConfig('wordpress_blog/search/min_word_length');
	}
	
	/**
	 * Retrieve the search route
	 *
	 * @return string
	 */
	public function getSearchRoute()
	{
		$route = trim(Mage::getStoreConfig('wordpress_blog/search/search_base'));
		
		return $route ? $route : 'search';
	}
	
	/**
	 * Determine whether to use SEO URL's on the search
	 *
	 * @return bool
	 */
	public function useSeoUrls()
	{
		return Mage::getStoreConfigFlag('wordpress_blog/search/use_seo_urls');
	}
	
	public function getResultsUrl($query = '')
	{
		if ($this->getEscapedSearchString()) {
			return $this->getUrl('search/' . $this->getEscapedSearchString() . '/');
		}
		
		return '';
	}
}
