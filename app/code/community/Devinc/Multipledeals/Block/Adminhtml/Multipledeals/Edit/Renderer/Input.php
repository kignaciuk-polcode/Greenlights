<?php

class Devinc_Multipledeals_Block_Adminhtml_Multipledeals_Edit_Renderer_Input extends Mage_Adminhtml_Block_Abstract implements Varien_Data_Form_Element_Renderer_Interface
{

    public function render(Varien_Data_Form_Element_Abstract $element) 
	{		  
			
        $html = '<tr><td class="label"><label for="'.$element->getId().'">'.$element->getLabelHtml().'</label></td><td class="value">'.$element->getElementHtml();
		$html .= '<p class="note" id="note_'.$element->getId().'"><span>'.$element->getNote().'</span></p>';		
		$html .= '</td>';
		$html .= '</tr>';
		
        return $html;
    }	
	

}
