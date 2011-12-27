<?php
class Webtex_Core_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $_validPrefix = 'Webtex_';
    
    public function getModuleList()
    {
        $modules = (array)Mage::getConfig()->getNode('modules')->children();
        foreach ($modules as $moduleId=>$moduleInfo) {
        	if (!$this->isValidModule($moduleId))
                unset($modules[$moduleId]);
            else
                $modules[$moduleId]->id = $moduleId;
        }
        return $modules;
    }
    
    public function isValidModule($moduleId)
    {
        if (0 === strpos($moduleId,$this->_validPrefix))
        {
            return true;
        }
        return false;
    }
}


