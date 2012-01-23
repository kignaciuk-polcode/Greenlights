<?php
class Devinc_Multipledeals_Block_Multipledeals extends Mage_Core_Block_Template
{	
    public function getNrViews()
	{
		$collection = Mage::getModel('multipledeals/multipledeals')->getCollection()->addFieldToFilter('status', 3);
		foreach ($collection as $prod) {
			$multipledeals_id = $prod->multipledeals_id;
		}
		$model = Mage::getModel('multipledeals/multipledeals');	
		$nr_views = $model->load($multipledeals_id)->getNrViews();
		$nr_views++;
		$model->setId($multipledeals_id)
			  ->setNrViews($nr_views)
			  ->save();		
	}
	
	public function getCountdown($width = null, $height = null, $id = null, $main_bg_color = null, $product_id)
    {
		$multipledeals = Mage::getModel('multipledeals/multipledeals')->getCollection()->addFieldToFilter('status', 3)->addFieldToFilter('product_id', $product_id)->getFirstItem();
		
		$startDate = Mage::getModel('core/date')->date('Y-m-d H,i,s');
		$endDate = $multipledeals->getDateTo().' '.$multipledeals->getTimeTo();
		$jsStartDate = Mage::getModel('core/date')->date('m/d/Y g:i A');
		$jsEndDate = date("m/d/Y g:i A", strtotime($multipledeals->getDateTo().' '.str_replace(',',':',$multipledeals->getTimeTo())));
		
		//js configuration
		$js_bg_main = Mage::getStoreConfig('multipledeals/js_countdown_configuration/bg_main');
		$js_textcolor = Mage::getStoreConfig('multipledeals/js_countdown_configuration/textcolor');
		$js_days_text = Mage::getStoreConfig('multipledeals/js_countdown_configuration/days_text');
		
		//flash configuration
		$countdown_type = Mage::getStoreConfig('multipledeals/configuration/countdown_type');
		$display_days = Mage::getStoreConfig('multipledeals/countdown_configuration/display_days');
		if (is_null($main_bg_color)) {
			$bg_main = str_replace('#','0x',Mage::getStoreConfig('multipledeals/countdown_configuration/bg_main'));
		} else {
			$bg_main = str_replace('#','0x',$main_bg_color);
		}
		$bg_color = str_replace('#','0x',Mage::getStoreConfig('multipledeals/countdown_configuration/bg_color'));
		$textcolor = str_replace('#','0x',Mage::getStoreConfig('multipledeals/countdown_configuration/textcolor'));
		$alpha = Mage::getStoreConfig('multipledeals/countdown_configuration/alpha');
		$sec_text = Mage::getStoreConfig('multipledeals/countdown_configuration/sec_text');
		$min_text = Mage::getStoreConfig('multipledeals/countdown_configuration/min_text');
		$hour_text = Mage::getStoreConfig('multipledeals/countdown_configuration/hour_text');
		$days_text = Mage::getStoreConfig('multipledeals/countdown_configuration/days_text');
		$smh_color = str_replace('#','0x',Mage::getStoreConfig('multipledeals/countdown_configuration/txt_color'));
			
		$date1 = mktime(Mage::getModel('core/date')->date('H'),Mage::getModel('core/date')->date('i'),Mage::getModel('core/date')->date('s'),Mage::getModel('core/date')->date('m'),Mage::getModel('core/date')->date('d'),Mage::getModel('core/date')->date('Y'));
	    $date2 = mktime(substr($multipledeals->getTimeTo(),0,2),substr($multipledeals->getTimeTo(),3,2),substr($multipledeals->getTimeTo(),6,2),substr($multipledeals->getDateTo(),5,2),substr($multipledeals->getDateTo(),8,2),substr($multipledeals->getDateTo(),0,4));	   
		$dateDiff = $date2 - $date1;
		
		$fullDays = floor($dateDiff/(60*60*24));
		if ($display_days==1) {
			if ($fullDays<=0) {
				$source = $this->getSkinUrl('multipledeals/flash/countdown.swf');
			} else {
				$source = $this->getSkinUrl('multipledeals/flash/countdown_days.swf');
			} 
		} else {
			if ($dateDiff>0) {
				$diff = abs($dateDiff); 
				$years   = floor($diff / (365*60*60*24)); 
				$months  = floor(($diff - $years * 365*60*60*24) / (30*60*60*24)); 
				$days    = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
				$hours   = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24)/ (60*60)); 
				
				$hours_left = $days*24+$hours;
				if ($hours_left<100) {
					$source = $this->getSkinUrl('multipledeals/flash/countdown_multiple_2.swf');	
				} else {
					$source = $this->getSkinUrl('multipledeals/flash/countdown_multiple_3.swf');	
				}
			} else {
				$source = $this->getSkinUrl('multipledeals/flash/countdown_multiple_2.swf');
			}
		}	
			
		if (substr($id,0,12)=='product_view') {
			if ($fullDays<=0) {
				$js_days_text = '';
				$js_font_size = '38px';
			} else {
				$js_font_size = '34px';		
			}
		} else {
			if ($fullDays<=0) {
				$js_days_text = '';
				$js_font_size = '28px';
			} else {
				$js_font_size = '16px';		
			}
		}
		
		if ($multipledeals->getStatus()==4) {
			$variables = base64_encode($startDate.'&&&'.$startDate.'&&&'.$alpha.'&&&'.$bg_color.'&&&'.$textcolor.'&&&'.$bg_main.'&&&'.$smh_color); 
		} else {
			$variables = base64_encode($startDate.'&&&'.$endDate.'&&&'.$alpha.'&&&'.$bg_color.'&&&'.$textcolor.'&&&'.$bg_main.'&&&'.$smh_color); 		
		}
		$variables_smhd = $sec_text.'|||'.$min_text.'|||'.$hour_text.'|||'.$days_text; 	
		
		$variables_new = '';
		$i = 0;
		while (strlen($variables)>0) {
			if ($i%2==0) {
				$variables_new .= substr($variables,0,10).'dMD';
			} else {
				$variables_new .= substr($variables,0,10).'Dmd';					
			}
			$variables = substr($variables,10,1000);
			$i++;
		}
		
		$variables_new = substr($variables_new,0,-3);
   
   		$html = '';
		if ($countdown_type==0) {
		$html .= 	'<div id="countdown_'.$id.'" style="padding:5px 0px 5px 0px;">';
		} else {
		$html .= 	'<div id="countdown_'.$id.'" style="padding:2px 0px 0px 0px;">';
		}
		$html .= 	'<script language="javascript">
						function calcage(secs, num1, num2) {
						  s = ((Math.floor(secs/num1))%num2).toString();
						  if (LeadingZero && s.length < 2)
						    s = "0" + s;
						  return "<b>" + s + "</b>";
						}
						
						function calcageDays(secs, num1, num2) {
						  s = ((Math.floor(secs/num1))%num2).toString();
						  return "<b>" + s + "</b>";
						}
						
						function CountBack(secs, id, DisplayFormat) {
						  element = document.getElementById(id);
						  if (secs < 0) {
						    element.innerHTML = FinishMessage;
						    return;
						  }
						  if (secs < 86400) {
						  DisplayStr = DisplayFormat.replace(/%%D%%/g, \'\');
						  } else {
						  DisplayStr = DisplayFormat.replace(/%%D%%/g, calcageDays(secs,86400,100000));  
						  }
						  DisplayStr = DisplayStr.replace(/%%H%%/g, calcage(secs,3600,24));
						  DisplayStr = DisplayStr.replace(/%%M%%/g, calcage(secs,60,60));
						  DisplayStr = DisplayStr.replace(/%%S%%/g, calcage(secs,1,60));
						
						  element.innerHTML = DisplayStr;
						  if (CountActive)
						    setTimeout("CountBack(" + (secs+CountStepper) + ", \'" + id + "\', \'" + DisplayFormat + "\')", SetTimeOutPeriod);
						}
						
						function putspan(backcolor, forecolor) {
						 document.write("<div style=\"clear:both;width:100%;text-align:center;\"><span id=\''.$id.'\' style=\'background-color:" + backcolor + 
						                "; color:" + forecolor + "\'></span></div>");
						}
						
						TargetDate = "'.$jsEndDate.'";
						NowDate = "'.$jsStartDate.'";
						BackColor = "'.$js_bg_main.'";
						ForeColor = "'.$js_textcolor.'";
						CountActive = true;
						CountStepper = -1;
						LeadingZero = true;
						DisplayFormat = "%%D%% '.$js_days_text.' %%H%%:%%M%%:%%S%%";
						FinishMessage = "00:00:00";
						
						CountStepper = Math.ceil(CountStepper);
						if (CountStepper == 0)
						  CountActive = false;
						var SetTimeOutPeriod = (Math.abs(CountStepper)-1)*1000 + 990;
						putspan(BackColor, ForeColor);
						var dthen = new Date(TargetDate);
						var dnow = new Date();
						
						if(CountStepper>0)
 						  ddiff = new Date(dnow-dthen);
						else
						  ddiff = new Date(dthen-dnow);
						gsecs = Math.floor(ddiff.valueOf()/1000);
						CountBack(gsecs, "'.$id.'", DisplayFormat);
						document.getElementById("'.$id.'").style.fontSize = "'.$js_font_size.'";
					</script>
					</div>';
			//$html .=	'<p><a href="http://www.adobe.com/go/getflashplayer"><img src="http://www.adobe.com/images/shared/download_buttons/get_flash_player.gif" alt="Get Adobe Flash player" /></a></p>';
		
			
		if ($countdown_type==1) {   
		$html .= 	'<script type="text/javascript">		
					var so = new SWFObject("'.$source.'", "countdown_'.$id.'", "'.$width.'", "'.$height.'", "9");
					so.addParam("menu", "false");
					so.addParam("salign", "MT");
					so.addParam("allowFullscreen", "true");	
					if (navigator.userAgent.indexOf("Opera") <= -1) {
						so.addParam("wmode", "opaque");		
					}
					so.addVariable("vs", "'.$variables_new.'");						
					so.addVariable("smhd", "'.$variables_smhd.'");						
					so.write("countdown_'.$id.'");
				 </script>';
		}		
	
        return $html;
    }
    
    public function getMainDeal() {
    	Mage::getModel('multipledeals/multipledeals')->refreshDeals();
    	
		if (Mage::getStoreConfig('multipledeals/sidebar_configuration/maindeal_featured')) {
			$_product = Mage::registry('product');
		
			if (!isset($_product)) {
				$_product_id = 0;
			} else {
				$_product_id = $_product->getId();
			}
		
			$main_deal_product_id = Mage::getModel('multipledeals/multipledeals')->getCollection()->addFieldToFilter('product_id', array('neq'=>$_product_id))->addFieldToFilter('status', array('eq'=>'3'))->addFieldToFilter('type', array('eq'=>'1'))->getFirstItem()->getProductId();
		
			return $main_deal_product_id;
		} else {
    		$main_deal_product_id = 0;
    	}
    }

	public function getItems()
    {
    	$main_deal_product_id = $this->getMainDeal();
		$_product = Mage::registry('product');
		
		if (!isset($_product)) {
			$_product_id = 0;
		} else {
			$_product_id = $_product->getId();
		}
		
		$multipledeals_collection = Mage::getModel('multipledeals/multipledeals')->getCollection()->addFieldToFilter('product_id', array('nin'=>array($_product_id,$main_deal_product_id)))->addFieldToFilter('status', array('eq'=>'3'))->setOrder('type', 'ASC')->setOrder('multipledeals_id', 'DESC');

		$multipledeals_product_id = array();
		$multipledeals_keys = array();
		$i = 0;  
		  
		foreach ($multipledeals_collection as $multipledeals) {      
			$multipledeals_product_id[] = $multipledeals->getProductId();          
			$multipledeals_keys[$multipledeals->getProductId()] = $i;
			$i++;
		}	  
		  
		$productCollection = Mage::getResourceModel('catalog/product_collection')
				->addAttributeToSelect('entity_id')
				->addAttributeToSelect('name')
				->addAttributeToSelect('thumbnail')
				->addAttributeToSelect('status')
				->addAttributeToSelect('price')
				->addAttributeToFilter('entity_id', array('in' => $multipledeals_product_id))
				->load();
		
		$productCollectionOrdered = array();
		
		foreach ($productCollection as $prod) {      
			$productCollectionOrdered[$multipledeals_keys[$prod->getId()]] = $prod;          
		}	  
		
		ksort($productCollectionOrdered);
				
        return $productCollectionOrdered;
    }
	
    public function getSpecialPrice($_product)
    {	
       	$currency_symbol = Mage::app()->getLocale()->currency(Mage::app()->getStore()->getCurrentCurrencyCode())->getSymbol();
		$baseCurrencyCode = Mage::app()->getStore()->getBaseCurrencyCode();
		$currentCurrencyCode = Mage::app()->getStore()->getCurrentCurrencyCode();		

		$_taxHelper  = $this->helper('tax');

		$_simplePricesTax = ($_taxHelper->displayPriceIncludingTax() || $_taxHelper->displayBothPrices());
				
		$multipledeals = Mage::getModel('multipledeals/multipledeals')->getCollection()->addFieldToFilter('status', array('eq' => 3))->addFieldToFilter('product_id', array('eq' => $_product->getId()))->getFirstItem();
		$special_price = $multipledeals->getDealPrice();
			
		$converted_special_price = Mage::helper('directory')->currencyConvert($special_price, $baseCurrencyCode, $currentCurrencyCode);
		$special_price_tax = $_taxHelper->getPrice($_product, $converted_special_price, $_simplePricesTax);		
		
		return $currency_symbol.number_format($special_price_tax,2);
    }	

    public function getPriceHtml($product)
    {
        $this->setTemplate('catalog/product/price.phtml');
        $this->setProduct($product);
        return $this->toHtml();
    }
}