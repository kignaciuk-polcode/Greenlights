<?php

class Fishpig_Wordpress_Model_Sitemap_Xml extends Varien_Object
{
	public function load()
	{
		$filename = $this->getFilename();
		
		$file = $this->_getVarienIoFileObject();
		
		if ($file->fileExists($filename)) {
			$this->setXml($file->read($filename));
			return true;	
		}
		
		return false;
	}
	
	public function save()
	{
		if ($this->getXml()) {
			$filename = $this->getFilename();
			$file = $this->_getVarienIoFileObject();
			
			$file->write($this->getFilename(), $this->getXml());
		}
		
		return $this;
	}


	public function getFilename()
	{
		if (!$this->hasFilename()) {
			$storeId = Mage::app()->isSingleStoreMode() ? 0 : Mage::app()->getStore()->getId();
			$this->setFilename(Mage::helper('wordpress')->getFileCachePath() . 'xml-sitemap-' . $storeId . '.xml');
		}
		
		return $this->getData('filename');
	}
	
	public function generate()
	{	
		$oldArea = Mage::getDesign()->getArea();
		Mage::getDesign()->setArea('frontend');

		$xml = Mage::getSingleton('core/layout')->createBlock('wordpress/sitemap_xml')
			->setTemplate('wordpress/sitemap/xml.phtml')
			->toHtml();
			
		$xml = trim(str_replace(array("\n", "\t"), '', $xml));
		
		Mage::getDesign()->setArea($oldArea);

		if (!$xml) {
			throw new Exception('Unable to create XML sitemap.');
		}
		
		return $this->setXml($xml);
	}
	
	protected function _getVarienIoFileObject()
	{
		$file = new Varien_Io_File();
		$file->setAllowCreateFolders(true);
		$file->open(array('path' => Mage::helper('wordpress')->getFileCachePath()));
		$file->setAllowCreateFolders(true);	
		
		return $file;
	}
}
