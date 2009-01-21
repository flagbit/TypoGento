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
 * TypoGento tools
 *
 * @version $Id$
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class tx_fbmagento_tools {
	
	/**
	 * returns the ExtConfig as Array
	 *
	 * @return array
	 */
	public static function getExtConfig(){
		return unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['fb_magento']);
	}
	
	public static function getFELangStoreCode(){
		//$GLOBALS['TYPO3_DB']->debugOutput = true;
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('tx_fbmagento_store', 'sys_language', sprintf('uid = %d', $GLOBALS['TSFE']->config['config']['sys_language_uid']));
		
		$res = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		if (! ($store = $res['tx_fbmagento_store']))
		{
			$store = 'default';
		}
		return $store;
	}
	
}

?>