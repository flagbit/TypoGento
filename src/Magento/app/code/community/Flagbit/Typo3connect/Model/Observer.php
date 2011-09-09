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
 * TypoGento Observer Model
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class Flagbit_Typo3connect_Model_Observer extends Mage_Core_Model_Abstract
{
	/**
	 * Set if the request is being made via SOAP or XMLRPC
	 * 
	 * @var boolean
	 */
	protected $_apiRequest;

	/**
	 * create or update an TYPO3 Frontend User
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function customerSaveAfterEvent($observer) {
		
		if (! Mage::getSingleton ( 'Flagbit_Typo3connect/Core' )->isEnabled ())
			return;
		
		// no TYPO3 db data given -> nothing to do
		if (!Mage::getStoreConfig ( 'typo3connect/typo3_db/host' ) || !Mage::getStoreConfig ( 'typo3connect/typo3_db/username' ) ||	!Mage::getStoreConfig ( 'typo3connect/typo3_db/password' ) || !Mage::getStoreConfig ( 'typo3connect/typo3_db/dbname' ))
			return;

		$customer = $observer->getCustomer();
		
		// assign the fields
		$fields = array (
			'username' => $customer->getData ( 'email' ), 
			'name' => $customer->getData ( 'lastname' ), 
			'firstname' => $customer->getData ( 'firstname' ), 
			'email' => $customer->getData ( 'email' ), 
			'password' => $customer->getData ( 'password' ), 
			'usergroup' => Mage::helper ( 'Flagbit_Typo3connect' )->getConfigData ( 'fe_user_group_uid' ), 
			'pid' => Mage::helper ( 'Flagbit_Typo3connect' )->getConfigData ( 'fe_user_pid' ), 
			'tx_fbmagento_id' => $customer->getId () 
		);
		
		try {
			// get fe_users Model
			$feUsers = Mage::getSingleton ( 'Flagbit_Typo3connect/typo3_frontend_user' );
			$customer->load ( $customer->getId () );
			
			if ($customer->getTypo3Uid ()) {
				$feUsers->setId ( $customer->getTypo3Uid () );
			}
			
			foreach ( $fields as $key => $value ) {
				$feUsers->setData ( $key, $value );
			}
			
			$feUsers->save ();
			$customer->setData ( 'typo3_uid', $feUsers->getData ( 'uid' ) );
			$customer->getResource ()->saveAttribute ( $customer, 'typo3_uid' );
		} catch (Exception $e) {
			Mage::log($e->getMessage());
		}
	}
	
	
	/**
	 * save typo3 group id
	 * 
	 * @param Varien_Event_Observer $observer
	 */
	public function customerGroupSaveBefore($observer){
		
		$observer->getObject()->setData('typo3_group_id', intval(Mage::app()->getRequest()->getParam('typo3_group_id')));

	}
	
	
	/**
	 * Check if raw access is permitted to the magento frontend
	 *
	 * @param Varien_Event_Observer $observer
	 */
	public function controllerActionPredispatch($observer)
	{
		if (Mage::getSingleton('Flagbit_Typo3connect/Core')->isEnabled()) return;
		
		if (! Mage::app()->getStore()->isAdmin() && ! $this->_isApiRequest())
		{
			if (! Mage::getStoreConfig('typo3connect/config/direct_mage_access'))
			{
				if ($uaRegex = Mage::getStoreConfig('typo3connect/config/ua_regex'))
				{
					if ($this->_checkUserAgentAgainstRegexps($uaRegex)) return;
				}
				Mage::app()->getResponse()->setRedirect(Mage::getStoreConfig('typo3connect/config/redirect_url'));
			}
		}
	}
	
	/**
	 * Return true if the request is being made via SOAP or XMLRPC
	 * 
	 * @return boolean
	 */
	protected function _isApiRequest()
	{
		return Mage::app()->getRequest()->getModuleName() === 'api';
	}
    
	/**
	 * Match the User Agent Header value agains the given regex
	 *
	 * @param string $regexp
	 * @return bool
	 */
	protected function _checkUserAgentAgainstRegexps($regexp)
	{
		if (!empty($_SERVER['HTTP_USER_AGENT'])) {
			if (!empty($regexp)) {
				if (false === strpos($regexp, '/', 0)) {
					$regexp = '/' . $regexp . '/';
				}
				if (@preg_match($regexp, $_SERVER['HTTP_USER_AGENT'])) return true;
			}
		}
		return false;
	}
}

