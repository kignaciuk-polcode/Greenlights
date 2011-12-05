<?php
/**
 * @category    Fishpig
 * @package     Fishpig_Wordpress
 * @license     http://fishpig.co.uk/license.txt
 * @author      Ben Tideswell <help@fishpig.co.uk>
 */

class Fishpig_Wordpress_Model_System_Config_Source_Recaptcha_Language
{
	public function toOptionArray()
	{
		$themes = array(
			'en' => 'English', 'nl' => 'Dutch',
			'fr' => 'French', 'de' => 'German',
			'pt' => 'Portuguese', 'ru' => 'Russian',
			'es' => 'Spanish', 'tr' => 'Turkish'
		);
		$options = array();
		
		foreach($themes as $value => $label) {
			$options[] = array('value' => $value, 'label' => Mage::helper('wordpress')->__($label));
		}
		
		return $options;
	}
}
