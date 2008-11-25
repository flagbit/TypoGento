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
 * TypoGento FeUsers Model
 *
 * @version $Id
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class Flagbit_Typo3connect_Model_Mysql4_Typo3_FeUsers extends Mage_Core_Model_Mysql4_Abstract {
	
	/**
	 * Constuctor
	 *
	 */
	protected function _construct() {
		$this->_init ( 'Flagbit_Typo3connect/typo3_feusers', 'uid' );
		$this->_resourcePrefix = 'Flagbit_Typo3connect';
	}
	
	/**
	 * Get connection by name or type
	 *
	 * @param   string $connectionName
	 * @return  Zend_Db_Adapter_Abstract
	 */
	protected function _getConnection($connectionName) {
		
		if (isset ( $this->_connections [$connectionName] )) {
			return $this->_connections [$connectionName];
		}
		
		$connConfig = Mage::getConfig ()->getResourceConnectionConfig ( 'Flagbit_Typo3connect_' . $connectionName );
		
		$typeInstance = $this->_resources->getConnectionTypeInstance ( ( string ) $connConfig->type );
		$this->_connections [$connectionName] = $typeInstance->getConnection ( $connConfig );
		
		return $this->_connections [$connectionName];
	}
	
	/**
	 * Get an TYPO3 fe_user
	 *
	 * @param   int unique ID
	 * @return  array
	 */
	public function getUserById($id) {
		$read = $this->_getReadAdapter ();
		$select = $read->select ();
		
		$select->from ( array ('main_table' => $this->getMainTable () ) )->where ( $this->getIdFieldName () . ' = ?', $id )->limit ( 1 );
		
		return $read->fetchRow ( $select );
	}

}

