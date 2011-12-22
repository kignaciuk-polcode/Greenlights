<?php

/**
 * ||GEISSWEB| EU-VAT-GROUPER
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the GEISSWEB End User License Agreement
 * that is available through the world-wide-web at this URL:
 * http://www.geissweb.de/eula.html
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@geissweb.de so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.geissweb.de/ for more information
 * or send an email to support@geissweb.de or visit our customer forum at
 * http://forum.geissweb.de to make a feature request.
 *
 * @category   Mage
 * @package    Geissweb_Euvatgrouper
 * @copyright  Copyright (c) 2011 GEISS Weblösungen (http://www.geissweb.de)
 * @license    http://www.geissweb.de/eula.html GEISSWEB End User License Agreement
 */
class Geissweb_Euvatgrouper_IndexController extends Mage_Core_Controller_Front_Action {

    public function indexAction()
    {
	$validator = Mage::getSingleton("geissweb_euvatgrouper/vatvalidation");

	if ($this->getRequest()->getParam('taxvat') != "")
	{
	    $validator->setUserTaxvat(trim(str_replace(array(" ", ".", ",", "-", "|"), "", $this->getRequest()->getParam('taxvat'))));
	    $do_validation = true;
	}
	elseif (is_array($this->getRequest()->getParam('billing')))
	{
	    $param = $this->getRequest()->getParam('billing');
	    $validator->setUserTaxvat(trim(str_replace(array(" ", ".", ",", "-", "|"), "", $param['taxvat'])));
	    $do_validation = true;
	}
	elseif ($this->getRequest()->getParam('taxvat') == "" && $this->getRequest()->getParam('vatid') == "removed")
	{
	    $do_validation = false;
	    $validator->assignDefault();
	}

	if ($do_validation)
	{
	    $validator->setUserCc(strtoupper(substr($validator->getUserTaxvat(), 0, 2)));
	    $validator->setUserNr(substr($validator->getUserTaxvat(), 2));
	    $validator->validate();
	    $result = $validator->getResult();
	    $this->getResponse()->setBody(Mage::helper('core')->jsonEncode($result));
	}
    }

//endFunction
}

//endClass
?>