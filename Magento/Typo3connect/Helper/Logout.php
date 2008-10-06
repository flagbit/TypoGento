<?php



class Flagbit_Typo3connect_Helper_Logout extends Mage_Core_Helper_Abstract {

	
	static public function logoutEvent($observer){
		
		if (! Mage::getSingleton ( 'Flagbit_Typo3connect/Core' )->isEnabled ()) return;
		

        $GLOBALS['TSFE']->fe_user->logoff();

	}
	
}

?>