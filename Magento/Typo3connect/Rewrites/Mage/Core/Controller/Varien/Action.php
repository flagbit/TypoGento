<?php 

class Mage_Core_Controller_Varien_Action extends Flagbit_Typo3connect_Rewrite_Mage_Core_Controller_Varien_Action {
	
	protected function _getRefererUrl() {
		return rtrim(parent::_getRefererUrl(), '/');
	}
	
}