<?php



class Flagbit_Typo3connect_Helper_Login extends Mage_Core_Helper_Abstract {

	/**
	 * called by Customer Login
	 *
	 * @param Varien_Event_Observer $observer
	 */
	static public function loginEvent($observer){
		
		if (! Mage::getSingleton ( 'Flagbit_Typo3connect/Core' )->isEnabled ()) return;
		
		$event = $observer->getEvent();
		
		/*@var $customer Mage_Customer_Model_Customer */
		$customer = $event->getCustomer();
	
        if($customer->getTypo3_uid()){
        	
        	$feUsers = Mage::getSingleton('Flagbit_Typo3connect/Typo3_FeUsers');
        	$tempuser = $feUsers->getUserById($customer->getTypo3_uid());
        	
        	$GLOBALS['TSFE']->fe_user->createUserSession($tempuser);
        }		

	}
	
}

?>