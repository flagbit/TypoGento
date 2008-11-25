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
 * TypoGento Customer Model
 *
 * @version $Id$
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class Flagbit_Typo3connect_Model_Customer extends Mage_Customer_Model_Customer {
	
	/**
	 * create or update an TYPO3 Frontend User
	 *
	 */
	public function _afterSave() {
		
		parent::_afterSave ();
		
		if (! Mage::getSingleton ( 'Flagbit_Typo3connect/Core' )->isEnabled ())
			return;
		
		// assign the fields
		$fields = array (
			'username' => $this->getData ( 'email' ), 
			'name' => $this->getData ( 'lastname' ), 
			'firstname' => $this->getData ( 'firstname' ), 
			'email' => $this->getData ( 'email' ), 
			'password' => $this->getData ( 'password' ), 
			'usergroup' => Mage::helper ( 'Flagbit_Typo3connect' )->getConfigData ( 'fe_user_group_uid' ), 
			'pid' => Mage::helper ( 'Flagbit_Typo3connect' )->getConfigData ( 'fe_user_pid' ), 
			'tx_fbmagento_id' => $this->getId () 
		);
		
		// get fe_users Model
		$feUsers = Mage::getSingleton ( 'Flagbit_Typo3connect/Typo3_FeUsers' );
		$this->load ( $this->getId () );
		
		if ($this->getTypo3_uid ()) {
			$feUsers->setId ( $this->getTypo3_uid () );
		}
		
		foreach ( $fields as $key => $value ) {
			$feUsers->setData ( $key, $value );
		}
		$feUsers->save ();
		
		$this->setData ( 'typo3_uid', $feUsers->getData ( 'uid' ) );
		$this->getResource ()->saveAttribute ( $this, 'typo3_uid' );
	
	}

}

?>