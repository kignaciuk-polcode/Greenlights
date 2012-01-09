<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

abstract class Fishpig_Wordpress_Model_Post_Attachment_Abstract extends Mage_Core_Model_Abstract
{
	/**
	 * Loads the associated attachment meta data
	 * This data is stored as a serialized array
	 *
	 * @param int $id
	 * @param string $field
	 * @return Fishpig_Wordpress_Model_Post_Attachment_Abstract
	 */
	public function load($id, $field = null)
	{
		parent::load($id, $field);
		
		$this->loadSerializedData();

		return $this;
	}
	
	/**
	 * Load the serialized attachment data
	 *
	 */
	public function loadSerializedData()
	{
		if ($this->getId() > 0 && !$this->getIsFullyLoaded()) {
			$this->setIsFullyLoaded(true);

			$select = Mage::helper('wordpress/db')->getReadAdapter()
				->select()
				->from(Mage::helper('wordpress/db')->getTableName('postmeta'), 'meta_value')
				->where('meta_key=?', '_wp_attachment_metadata')
				->where('post_id=?', $this->getId())
				->limit(1);

			$data = unserialize(Mage::helper('wordpress/db')->getReadAdapter()->fetchOne($select));

			if (is_array($data)) {
				foreach($data as $key => $value) {
					$this->setData($key, $value);
				}			
			}
		}
	}
}
