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
 * TypoGento tcafields
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */

class tx_fbmagento_tcafields {

	/**
	 * generates an Productlist as Array for TCA Select fields
	 *
	 * @param array $params
	 * @param object $pObj
	 */
	public function itemsProcFunc_products(&$params,&$pObj) {

		$conf = tx_fbmagento_tools::getExtConfig();

		try {

			$soapClient = new tx_fbmagento_soapinterface($conf['url'], $conf['username'], $conf['password']);
			$products = $soapClient->catalog_product()->list();

		} catch (Exception $e) {
			tx_fbmagento_tools::displayError('SOAP API Error: ' . $e->getMessage());
		}

		foreach ((array) $products as $product) {
			$params['items'][]=Array($product['name'] . ' - ' . $product['sku'], $product['product_id']);
		}
	}

	/**
	 * generates a Grouplist as Array for TCA Select fields
	 *
	 * @param array $params
	 * @param object $pObj
	 */
	public function itemsProcFunc_usergroups(&$params,&$pObj) {

		$conf = tx_fbmagento_tools::getExtConfig();

		try {

			$soapClient = new tx_fbmagento_soapinterface($conf['url'], $conf['username'], $conf['password']);
			$roles = $soapClient->typo3connect_admin_roles()->list();

		} catch (Exception $e) {
			tx_fbmagento_tools::displayError('SOAP API Error: ' . $e->getMessage());
		}

		foreach ((array) $roles as $role) {
			$params['items'][]=Array($role['label'], $role['value']);
		}
	}

	/**
	 * generates a frontend Grouplist as Array for TCA Select fields
	 *
	 * @param array $params
	 * @param object $pObj
	 */
	public function itemsProcFunc_feusergroups(&$params,&$pObj) {

		$conf = tx_fbmagento_tools::getExtConfig();

		try {

			$soapClient = new tx_fbmagento_soapinterface($conf['url'], $conf['username'], $conf['password']);
			$roles = $soapClient->customer_group()->list();

		} catch (Exception $e) {
			tx_fbmagento_tools::displayError('SOAP API Error: ' . $e->getMessage());
		}

		foreach ((array) $roles as $role) {
			$params['items'][]=Array($role['customer_group_code'], $role['customer_group_id']);
		}
	}

	/**
	 * generates an Modulelist as Array for TCA Select fields
	 *
	 * @param array $params
	 * @param object $pObj
	 */
	public function itemsProcFunc_modules(&$params,&$pObj) {

		$conf = tx_fbmagento_tools::getExtConfig();

		try {

			$soapClient = new tx_fbmagento_soapinterface($conf['url'], $conf['username'], $conf['password']);
			$modules = $soapClient->typo3connect_modules()->list();

		} catch (Exception $e) {
			tx_fbmagento_tools::displayError('SOAP API Error: ' . $e->getMessage());
		}

		foreach ((array) $modules as $module) {
			$params['items'][]=Array(ucfirst($module), $module);
		}
	}

	/**
	 * generates an Controllerlist as Array for TCA Select fields
	 *
	 * @param array $params
	 * @param object $pObj
	 */
	public function itemsProcFunc_controllers(&$params,&$pObj) {

		$module = $this->getFlexformData($pObj, 'route', 'main');

		if (!$module) return;

		$conf = tx_fbmagento_tools::getExtConfig();

		try {

			$soapClient = new tx_fbmagento_soapinterface($conf['url'], $conf['username'], $conf['password']);
			$controllers = $soapClient->typo3connect_modules()->controllers($module);

		} catch (Exception $e) {
			tx_fbmagento_tools::displayError('SOAP API Error: ' . $e->getMessage());
		}		

		foreach ((array) $controllers as $controller) {
			$params['items'][]=Array(ucfirst($controller), $controller);
		}
	}

	/**
	 * generates an Actionlist as Array for TCA Select fields
	 *
	 * @param array $params
	 * @param object $pObj
	 */
	public function itemsProcFunc_actions(&$params,&$pObj) {

		$module = $this->getFlexformData($pObj, 'route', 'main');
		if (!$module) return;

		$controller = $this->getFlexformData($pObj, 'controller', 'main');
		if (!$controller) return;

		$conf = tx_fbmagento_tools::getExtConfig();

		try {

			$soapClient = new tx_fbmagento_soapinterface($conf['url'], $conf['username'], $conf['password']);
			$actions = $soapClient->typo3connect_modules()->actions($module, $controller);

		} catch (Exception $e) {
			tx_fbmagento_tools::displayError('SOAP API Error: ' . $e->getMessage());
		}

		foreach ((array) $actions as $action){
			$params['items'][]=Array($action, $action);
		}
	}

	/**
	 * returns the Value of an Flexform Field from TCEforms
	 *
	 * @param t3lib_TCEforms $TCEforms
	 * @param string $fieldName
	 * @param string $sheet
	 * @param string $lang
	 * @param string $value
	 * @return unknown
	 */
	protected function getFlexformData(t3lib_TCEforms &$TCEforms, $fieldName, $sheet='sDEF',$lang='lDEF',$value='vDEF') {

		try {
			$data = current($TCEforms->cachedTSconfig);
			$flexform = $data['_THIS_ROW']['pi_flexform'];
			$flexformArray = t3lib_div::xml2array($flexform);

			return $this->getFFvalue($flexformArray, $fieldName, $sheet, $lang, $value);

		} catch (Exception $e) {
			return null;
		}
	}

	/**
	 * Return value from somewhere inside a FlexForm structure
	 *
	 * @param	array		FlexForm data
	 * @param	string		Field name to extract. Can be given like "test/el/2/test/el/field_templateObject" where each part will dig a level deeper in the FlexForm data.
	 * @param	string		Sheet pointer, eg. "sDEF"
	 * @param	string		Language pointer, eg. "lDEF"
	 * @param	string		Value pointer, eg. "vDEF"
	 * @return	string		The content.
	 */
	protected function getFFvalue($T3FlexForm_array,$fieldName,$sheet='sDEF',$lang='lDEF',$value='vDEF') {
		$sheetArray = is_array($T3FlexForm_array) ? $T3FlexForm_array['data'][$sheet][$lang] : '';
		if (is_array($sheetArray)) {
			return $this->getFFvalueFromSheetArray($sheetArray,explode('/',$fieldName),$value);
		}
	}

	/**
	 * Returns part of $sheetArray pointed to by the keys in $fieldNameArray
	 *
	 * @param	array		Multidimensiona array, typically FlexForm contents
	 * @param	array		Array where each value points to a key in the FlexForms content - the input array will have the value returned pointed to by these keys. All integer keys will not take their integer counterparts, but rather traverse the current position in the array an return element number X (whether this is right behavior is not settled yet...)
	 * @param	string		Value for outermost key, typ. "vDEF" depending on language.
	 * @return	mixed		The value, typ. string.
	 * @access private
	 * @see pi_getFFvalue()
	 */
	protected function getFFvalueFromSheetArray($sheetArray,$fieldNameArr,$value) {

		$tempArr = $sheetArray;

		foreach ($fieldNameArr as $k => $v) {
			if (t3lib_div::testInt($v)) {
				if (is_array($tempArr)) {
					$c = 0;

					foreach ($tempArr as $values) {
						if ($c == $v) {
							$tempArr = $values;
							break;
						}

						$c++;
					}
				}
			} else {
				$tempArr = $tempArr[$v];
			}
		}
		return $tempArr[$value];
	}

	/**
	 * generates an Storeviewlist as Array for TCA Select fields
	 *
	 * @param array $params
	 * @param object $pObj
	 */
	public function itemsProcFunc_languages(&$params,&$pObj) {

		$conf = tx_fbmagento_tools::getExtConfig();

		try {

			$soapClient = new tx_fbmagento_soapinterface($conf['url'], $conf['username'], $conf['password']);
			$storeviews = $soapClient->typo3connect_storeviews()->list();

		} catch (Exception $e) {
			tx_fbmagento_tools::displayError('SOAP API Error: ' . $e->getMessage());
		}

		foreach ((array) $storeviews as $storeview) {
			$params['items'][]=Array($storeview['label'], $storeview['value']);
		}
	}

	/**
	 * generates an Category as Array for TCA Select fields
	 *
	 * @param array $params
	 * @param object $pObj
	 */	
	public function itemsProcFunc_categories(&$params,&$pObj) {

		$conf = tx_fbmagento_tools::getExtConfig();

		try {

			$soapClient = new tx_fbmagento_soapinterface($conf['url'], $conf['username'], $conf['password']);
			$categories = $soapClient->catalog_category()->tree();

		} catch (Exception $e) {
			tx_fbmagento_tools::displayError('SOAP API Error: ' . $e->getMessage());
		}

		$this->getCategoryItems($params['items'], array($categories));
	}

	/**
	 * generates an recursive list of Categories
	 *
	 * @param array $items
	 * @param array $categories
	 */
	protected function getCategoryItems(&$items, $categories) {

		foreach ($categories as $category) {
			$items[] = array(str_repeat('-', $category['level']*2) . $category['name'], $category['category_id']);
			if (is_array($category['children'])) {
				$this->getCategoryItems($items, $category['children'], $category['level']);
			}
		}
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fb_magento/lib/class.tx_fbmagento_tcafields.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fb_magento/lib/class.tx_fbmagento_tcafields.php']);
}

?>
