<?php
class Flagbit_Typo3connect_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getConfigData($field)
    {
        $path = 'typo3connect/config/'.$field;
		$config = Mage::getStoreConfig($path, Mage::app()->getStore());		
        return $config;
    } 	
}
