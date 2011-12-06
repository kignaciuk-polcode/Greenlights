<?php

class Ebizmarts_Mailchimp_Model_Mysql4_Bulk_Collection extends Varien_Data_Collection_Filesystem
{
    /**
     * Folder, where all backups are stored
     *
     * @var string
     */
    protected $_baseDir;

    /**
     * Set collection specific parameters and make sure backups folder will exist
     */
    public function __construct()
    {
        parent::__construct();

        $this->_baseDir = Mage::getBaseDir('var') . DS . Ebizmarts_Mailchimp_Model_BulkSynchro::FLDR;

        // check for valid base dir
        $ioProxy = new Varien_Io_File();
        $ioProxy->mkdir($this->_baseDir);
        if (!is_file($this->_baseDir . DS . '.htaccess')) {
            $ioProxy->open(array('path' => $this->_baseDir));
            $ioProxy->write('.htaccess', 'deny from all', 0644);
        }

        // set collection specific params
        $this
            ->setOrder('time', self::SORT_ORDER_DESC)
            ->addTargetDir($this->_baseDir)
            ->setFilesFilter('/^[a-z0-9\-\_]+\.' . preg_quote(Ebizmarts_Mailchimp_Model_BulkSynchro::FILE_EXTENSION.".".Ebizmarts_Mailchimp_Model_BulkSynchro::BULK_EXTENSION, '/') . '$/')
            ->setCollectRecursively(false)
        ;
    }

    /**
     * Get backup-specific data from model for each row
     *
     * @param string $filename
     * @return array
     */
    protected function _generateRow($filename){

        $row = parent::_generateRow($filename);
		$items = Mage::getSingleton('mailchimp/bulkSynchro')->loadFile($row['basename'], $this->_baseDir)->getData();
        foreach ($items  as $key => $value) {
            $row[$key] = $value;
        }
        $row['size'] = filesize($filename);
        $row['updated_time'] =  date("F d Y H:i:s.", filemtime($filename));
        $row['updated_object'] =  new Zend_Date((int)filemtime($filename));
        return $row;
    }
}
