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
 * TypoGento Data Helper
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class Flagbit_Typo3connect_Helper_Data extends Mage_Core_Helper_Abstract
{

	/**
	 * returns Config Data
	 *
	 * @param string $field
	 * @return array config
	 */
	public function getConfigData($field)
    {
        $path = 'typo3connect/config/'.$field;
		$config = Mage::getStoreConfig($path, Mage::app()->getStore());		
        return $config;
    } 	
}
