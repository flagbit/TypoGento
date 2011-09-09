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
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class Flagbit_Typo3connect_Model_Mysql4_Typo3_Abstract extends Mage_Core_Model_Mysql4_Abstract {
	
	
	/**
	 * Class Constructor
	 *
	 */
    protected function _construct()
    {  
        parent::_construct();
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
		
		foreach (array('host', 'username', 'password', 'dbname') as $field)
		{
			$connConfig->{$field} = (string) Mage::getStoreConfig('typo3connect/typo3_db/' . $field);
		}
		
		$typeInstance = $this->_resources->getConnectionTypeInstance ( ( string ) $connConfig->type );
		$this->_connections [$connectionName] = $typeInstance->getConnection ( $connConfig );
		
		return $this->_connections [$connectionName];
	}
	

}

