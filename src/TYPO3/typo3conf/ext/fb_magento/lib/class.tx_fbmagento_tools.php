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
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class tx_fbmagento_tools {

	/**
	 * returns the ExtConfig as Array
	 *
	 * @return array
	 */
	public static function getExtConfig() {
		return unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['fb_magento']);
	}

	/**
	 * throws an Exeption
	 *
	 * @param string $message
	 */
	public static function throwException($message) {

		throw new Exception($message);
	}

	/**
	 * displays an Error Message
	 *
	 * @param string $warning
	 * @return unknown
	 */
	public static function displayError($warning, $stop=true) {
		if (class_exists('t3lib_exception')) {
			throw new t3lib_exception($warning);
		}

		$warning = '<h3>TYPOGENTO</h3>' . $warning;

		t3lib_BEfunc::typo3PrintError('', $warning, '', $stop ? 0 : 1);

		if ($stop) {
			die();
		}
	}

	/**
	 * get Frontend Languagecode
	 *
	 * @return string
	 */
	public static function getFELangStoreCode() {

		if (empty($GLOBALS['TSFE']->config['config']['sys_language_uid'])) {
			if ($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_fbmagento_pi1.']['storeName']) {
				return $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_fbmagento_pi1.']['storeName'];
			}
			return 'default';
		}

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'tx_fbmagento_store', 'sys_language', sprintf('uid = %d', $GLOBALS['TSFE']->config['config']['sys_language_uid'])
		);

		$res = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		if (!($store = $res['tx_fbmagento_store'])) {
			if ($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_fbmagento_pi1.']['storeName']) {
				$store = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_fbmagento_pi1.']['storeName'];
			} else {
				$store = 'default';
			}
		}
		return $store;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fb_magento/lib/class.tx_fbmagento_tools.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fb_magento/lib/class.tx_fbmagento_tools.php']);
}

?>