<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Netresearch_OPS
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * OPS payment DirectLink Model
 */
class Netresearch_OPS_Model_Api_DirectLink extends Mage_Core_Model_Abstract
{

    /**
     * Perform a CURL call and log request end response to logfile
     *
     * @param array $params
     * @return mixed
     */
     public function call($params, $url)
     {
         try {
             $http = new Varien_Http_Adapter_Curl();
             $config = array('timeout' => 30);
             $http->setConfig($config);
             $http->write(Zend_Http_Client::POST, $url, '1.1', array(), http_build_query($params));
             $response = $http->read();
             $response = substr($response, strpos($response, "<?xml"), strlen($response));
             return $response;
        } catch (Exception $e) {
            Mage::logException($e);
            Mage::throwException(
                Mage::helper('ops')->__('Ogone server is temporarily not available, please try again later.')
            );
        }
        
        return $response;
     }

    /**
     * Performs a POST request to the Direct Link Gateway with the given
     * parameters and returns the result parameters as array
     *
     * @param array $params
     * @return array
     */
     public function performRequest($requestParams, $url)
     {
        $helper = Mage::helper('ops');
        $requestParams = array_merge($requestParams,$this->buildAuthenticationParams()); //Merge Logic Operation Data with Authentication Data
        $requestParams['SHASIGN'] = Mage::helper('ops/payment')->shaCrypt(Mage::helper('ops/payment')->getSHASign($requestParams));
        $encodedParams = array();
        foreach ($requestParams as $key=>$value) {
            $encodedParams[$key] = utf8_decode($value);
        }

        $responseParams = $this->getParamArrFromXmlString(
            $this->call($encodedParams, $url)
        );

        $helper = Mage::helper('ops');
        $helper->log($helper->__("Direct Link Request/Response in Ogone \n\nRequest: %s\nResponse: %s\nMagento-URL: %s\nAPI-URL: %s",
            Zend_Json::encode($encodedParams),
            Zend_Json::encode($responseParams),
            Mage::helper('core/url')->getCurrentUrl(),
            $url
        ));
        
        $this->checkResponse($responseParams);

        return $responseParams;

     }

    /**
     * Return Authentication Params for OPS Call
     *
     * @return array
     */
     protected function buildAuthenticationParams()
     {
         return array(
             'PSPID' => Mage::getModel('ops/config')->getPSPID(),
             'USERID' => Mage::getModel('ops/config')->getApiUserId(),
             'PSWD' => Mage::getModel('ops/config')->getApiPswd(),
         );
     }

     /**
     * Parses the XML-String to an array with the result data
     *
     * @param string xmlString
     * @return array
     */
     public function getParamArrFromXmlString($xmlString)
     {
         try {
             $xml = new SimpleXMLElement($xmlString);
             foreach($xml->attributes() as $key => $value) {
                 $arrAttr[$key] = (string)$value;
             }
             foreach($xml->children() as $child) {
                 $arrAttr[$child->getName()] = (string) $child;
             }
             return $arrAttr;
         } catch (Exception $e) {
             Mage::log('Could not convert string to xml in ' . __FILE__ . '::' . __METHOD__ . ': ' . $xmlString);
             Mage::logException($e);
         }
     }
     
     /**
     * Check if the Response from OPS reports Errors
     *
     * @param array $responseParams
     * @return mixed
     */
     public function checkResponse($responseParams)
     {
         if ($responseParams['NCERROR'] > 0):
            if (empty($responseParams['NCERRORPLUS'])) {
                $responseParams['NCERRORPLUS'] = Mage::helper('ops')->__('Invalid payment information')." Errorcode:".$responseParams['NCERROR'];
            }
            
            //avoid exception if STATUS is set with special values
            if (isset($responseParams['STATUS']) && is_numeric($responseParams['STATUS'])):
                return;
            endif;
            
            Mage::throwException(
                Mage::helper('ops')->__('An error occured during the Ogone request. Your action could not be executed. Message: "%s".',$responseParams['NCERRORPLUS'])
            );
         endif;
     }
}
