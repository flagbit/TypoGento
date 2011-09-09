<?php
/*                                                                        *
 * This script is part of the TypoGento project 						  *
 *                                                                        *
 * TypoGento is free software; you can redistribute it and/or modify it   *
 * under the terms of the GNU General Public License version 2 as         *
 * published by the Free Software Foundation.                             *
 *                                                                        *
 * This script is distributed in the hope that it will be useful, but     *
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHAN-    *
 * TABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General      *
 * Public License for more details.                                       *
 *                                                                        */

/**
 * TypoGento Login Helper
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class Flagbit_Typo3connect_Helper_Login extends Mage_Core_Helper_Abstract {

	/**
	 * called by Customer Login, create TYPO3 fe_users Session
	 *
	 * @param Varien_Event_Observer $observer
	 */
	static public function loginEvent($observer){
		
		if (! Mage::getSingleton ( 'Flagbit_Typo3connect/Core' )->isEnabled ()) return;
		
		$event = $observer->getEvent();
		
		/*@var $customer Mage_Customer_Model_Customer */
		$customer = $event->getCustomer();
	
        if($customer->getTypo3_uid()){
        	
        	$feUsers = Mage::getSingleton('Flagbit_Typo3connect/typo3_frontend_user');
        	$tempuser = $feUsers->getUserById($customer->getTypo3_uid());
        	
        	$GLOBALS['TSFE']->fe_user->createUserSession($tempuser);
        }		

	}
	
}

?>