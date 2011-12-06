<?php
/**
 * Netresearch_OPS_ApiController
 * 
 * @package   
 * @copyright 2011 Netresearch
 * @author    Thomas Kappel <thomas.kappel@netresearch.de> 
 * @author    André Herrn <andre.herrn@netresearch.de> 
 * @license   OSL 3.0
 */
class Netresearch_OPS_ApiController extends Netresearch_OPS_Controller_Abstract
{
    /**
     * Order instance
     */
    protected $_order;

    /*
     * Predispatch to check the validation of the request from OPS
     */
    public function preDispatch()
    {
        if (!$this->_validateOPSData()) {
            throw new Exception ("Hash not valid");
        }
    }

    /**
     * Action to control postback data from ops
     *
     */
    public function postBackAction()
    {
        $params = $this->getRequest()->getParams();
        try {
            $this->getPaymentHelper()->applyStateForOrder(
                $this->_getOrder(),
                $params
            );
        } catch (Exception $e) {
            Mage::log("Fatal Exception in postBackAction:" .$e->getMessage());
            $this->_redirect('checkout/cart');
            return;
        }
    }
    
    /**
     * Action to control postback data from ops
     *
     */
    public function directLinkPostBackAction()
    {
        $params = $this->getRequest()->getParams();
        try {
            $this->getDirectlinkHelper()->processFeedback(
                $this->_getOrder(),
                $params
            );
        } catch (Exception $e) {
            $msq = "Fatal Exception in directLinkPostBackAction:" .$e->getMessage();
            Mage::log($msq);
            die($msq);
        }
    }
}
