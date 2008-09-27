<?php

require_once ('app/code/core/Mage/Core/Helper/Abstract.php');

class Flagbit_Typo3connect_Helper_Login extends Mage_Core_Helper_Abstract {

	/**
	 * called by Customer Login
	 *
	 * @param Varien_Event_Observer $observer
	 */
	static public function loginEvent($observer){
		$event = $observer->getEvent();
		
		/*@var $customer Mage_Customer_Model_Customer */
		$customer = $event->getCustomer();
		//echo $customer->getId();
		#die(get_class($customer));
	}
	
}

?>