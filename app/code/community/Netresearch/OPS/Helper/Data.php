<?php
/**
 * Netresearch_OPS_Helper_Data
 * 
 * @package   
 * @copyright 2011 Netresearch
 * @author    Thomas Kappel <thomas.kappel@netresearch.de>
 * @author    André Herrn <andre.herrn@netresearch.de>
 * @license   OSL 3.0
 */
class Netresearch_OPS_Helper_Data extends Mage_Core_Helper_Abstract
{
    const LOG_FILE_NAME = 'ops.log';

    /**
     * Returns config model
     * 
     * @return Netresearch_OPS_Model_Config
     */
    public function getConfig()
    {
        return Mage::getSingleton('ops/config');
    }
    
    public function getModuleVersionString()
    {
        $version = Mage::getConfig()->getNode("modules/Netresearch_OPS/version");
        $plainversion = str_replace(".","",$version);
        $versionString = "OPSMAGv".substr($plainversion,0,2);
        return $versionString;
    }
    
    /**
     * Checks if logging is enabled and if yes, logs given message to logfile
     * 
     * @param string $message
     * @param int $level
     */
    public function log($message, $level = null)
    {
        $separator = "\n"."===================================================================";
        if($this->getConfig()->shouldLogRequests()):  
            Mage::log($message.$separator, $level, self::LOG_FILE_NAME);
        endif;
    }
    
    public function redirect($url)
    {
        Mage::app()->getResponse()->setRedirect($url);
        Mage::app()->getResponse()->sendResponse();
        exit();
    }

    /**
     * Redirects to the given order and prints some notice output
     *
     * @param int $orderId
     * @param string $message
     * @return void
    */
    public function redirectNoticed($orderId, $message)
    {
        Mage::getSingleton('core/session')->addNotice($message);
        $this->redirect(
            Mage::getUrl('*/sales_order/view', array('order_id' => $orderId))
        );
    }

    public function getStatusText($statusCode)
    {
        $translationOrigin = "STATUS_".$statusCode;
        $translationResult = $this->__($translationOrigin);
        if ($translationOrigin != $translationResult):
            return $translationResult. " ($statusCode)";
        else:
            return $statusCode;
        endif;
    }
}
