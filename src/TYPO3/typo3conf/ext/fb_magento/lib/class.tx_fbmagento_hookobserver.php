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
 * TypoGento hookobserver
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class tx_fbmagento_hookobserver {

	/**
	 * logoff Hook
	 * 
	 * @param array $params
	 * @param t3lib_userAuth $pObj
	 */
	public function logoff($params, &$pObj){
		if (t3lib_div::GPvar('logintype') != 'logout'
			or $pObj->loginType != 'FE'){

			return;
		}

		// get Extension Config
		$this->emConf = tx_fbmagento_tools::getExtConfig();

		// get an Magento Instance
		$this->mage = t3lib_div::makeInstance('tx_fbmagento_interface', $this->emConf );
		$this->mage->connector->logout();

	}


	/**
	 * register autoloader Hook
	 * 
	 * @param array empty array
	 * @param array empty array
	 */
	public function registerAutoloader($params, &$pObj) {
		t3lib_div::makeInstance('tx_fbmagento_autoloader', tx_fbmagento_tools::getExtConfig());
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fb_magento/lib/class.tx_fbmagento_hookobserver.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fb_magento/lib/class.tx_fbmagento_hookobserver.php']);
}

?>
