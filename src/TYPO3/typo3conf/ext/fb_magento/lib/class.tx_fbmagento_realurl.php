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
 * TypoGento Realurl
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class tx_fbmagento_realurl {

	/**
	 * @var tx_realurl
	 */
	protected $_realurlRef = null;

	/**
	 * direction
	 *
	 * @var string
	 */
	protected $_direction = null;

	/**
	 * rewrite Category
	 *
	 * @param array $params
	 * @param object tx_realurl $ref
	 * @return string
	 */
	public function idRewrite($params, $ref) {

		$this->setConfig($params, $ref);

		if ($this->isRouteControllerAction('catalog', 'category', 'view')) {

			$cfg = array(
				'alias_field' => 'name',
				'table' => 'typo3connect_catalog_category.urlkeys',
				'type' => 'idList',
				'storeview' => 1,
				'cache' => 'memory'
			);

			return $this->rewriter($cfg, $params);
		}

		return $params['value'] ? $params['value'] : 0;
	}	

	/**
	 * rewrite Category
	 *
	 * @param array $params
	 * @param object tx_realurl $ref
	 * @return string
	 */
	public function categoryRewrite($params, $ref) {

		$this->setConfig($params, $ref);
		
		$cfg = array(
			'alias_field' => 'name',
			'table' => 'typo3connect_catalog_category.urlkeys',
			'type' => 'idList',
			'storeview' => 1,
			'cache' => 'memory'
		);

		return $this->rewriter($cfg, $params);
	}

	/**
	 * check Route, Controller and Action
	 *
	 * @param string $route
	 * @param string $controller
	 * @param string $action
	 * @return boolan
	 */
	protected function isRouteControllerAction($route, $controller = null, $action = null){

		switch($this->_direction){

			case 'encode':

				$params = (array) $this->getRealurlRef()->orig_paramKeyValues;

				if ($params['tx_fbmagento[shop][route]'] != $route){
					return false;
				}

				if ($controller !== null && $params['tx_fbmagento[shop][controller]'] != $controller) {
					return false;
				}

				if ($action !== null && $params['tx_fbmagento[shop][action]'] != $action) {
					return false;
				}

				break;

			case 'decode':

				$params = $this->getRealurlRef()->speakingURIpath_procValue;
				preg_match('|/shop/([^/]*)/([^/]*)/([^/]*)|', $params, $matches);

				if ($matches[1] != $route) {
					return false;
				}

				if ($controller !== null && $matches[2] != $controller) {
					return false;
				}

				if ($action !== null && $matches[3] != $action) {
					return false;
				}
				break;
		}

		return true;
	}

	/**
	 * primary Rewriter
	 *
	 * @param array $cfg
	 * @param array $params
	 * @param string $extValues
	 * @return string
	 */
	protected function rewriter($cfg, $params) {

		$cfg['id_field'] = 'id';

		if ($params ['decodeAlias']) {
			return $this->alias2id($cfg, $params['value']);
		} else {
			return $this->id2alias($cfg, $params['value']);
		}
	}

	/**
	 * set Config
	 *
	 * @param array $params
	 * @param tx_realurl $ref
	 * @return $this
	 */
	protected function setConfig($params, $ref) {

		$this->_realurlRef = $ref;

		if ($params['decodeAlias']) {
			$this->_direction = 'decode';
		} else {
			$this->_direction = 'encode';
		}

		return $this;
	}

	/**
	 * get Reference from tx_realurl
	 *
	 * @return object tx_realurl
	 */
	protected function getRealurlRef() {
		return $this->_realurlRef;
	}

	/**
	 * Looks up an ID value (integer) in lookup-table based on input alias value.
	 * (The lookup table for id<->alias is meant to contain UNIQUE alias strings for id integers)
	 * In the lookup table 'tx_realurl_uniqalias' the field "value_alias" should be unique (per combination of field_alias+field_id+tablename)! However the "value_id" field doesn't have to; that is a feature which allows more aliases to point to the same id. The alias selected for converting id to alias will be the first inserted at the moment. This might be more intelligent in the future, having an order column which can be controlled from the backend for instance!
	 *
	 * @param array Configuration array
	 * @param string Alias value to convert to ID
	 * @param boolean <code>true</code> if only non-expiring record should be looked up
	 * @return integer ID integer. If none is found: false
	 * @see lookUpTranslation(), lookUp_idToUniqAlias()
	 */
	protected function lookUp_uniqAliasToId($cfg, $aliasValue, $onlyNonExpired = FALSE) {

		// Look up the ID based on input alias value:
		$res = $GLOBALS ['TYPO3_DB']->exec_SELECTquery(
			'value_id',
			'tx_realurl_uniqalias',
			'value_alias=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($aliasValue, 'tx_realurl_uniqalias' )
				. ' AND field_alias=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($cfg['alias_field'], 'tx_realurl_uniqalias')
				. ' AND field_id=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($cfg['id_field'], 'tx_realurl_uniqalias')
				. ' AND tablename=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($cfg ['table'], 'tx_realurl_uniqalias')
				. ' AND ' . ($onlyNonExpired ? 'expire=0' : '(expire=0 OR expire>' . time () . ')')
		);

		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		$GLOBALS['TYPO3_DB']->sql_free_result($res);
		if ($row) {
			return $row['value_id'];
		}
		return null;
	}

	/**
	 * Looks up a alias string in lookup-table based on input ID value (integer)
	 * (The lookup table for id<->alias is meant to contain UNIQUE alias strings for id integers)
	 *
	 * @param array Configuration array
	 * @param string ID value to convert to alias value
	 * @param integer sys_language_uid to use for lookup
	 * @param string Optional alias value to limit search to
	 * @return string Alias string. If none is found: false
	 * @see lookUpTranslation(), lookUp_uniqAliasToId()
	 */
	protected function lookUp_idToUniqAlias($cfg, $idValue, $lang, $aliasValue = '') {

		// Look for an alias based on ID:
		list($row) = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'value_alias',
			'tx_realurl_uniqalias',
			'value_id=' . $GLOBALS ['TYPO3_DB']->fullQuoteStr($idValue, 'tx_realurl_uniqalias')
				. ' AND field_alias=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($cfg['alias_field'], 'tx_realurl_uniqalias' )
				. ' AND field_id=' . $GLOBALS ['TYPO3_DB']->fullQuoteStr ( $cfg ['id_field'], 'tx_realurl_uniqalias' )
				. ' AND tablename=' . $GLOBALS ['TYPO3_DB']->fullQuoteStr ( $cfg ['table'], 'tx_realurl_uniqalias' )
				. ' AND lang=' . intval ( $lang )
				. ' AND expire=0'
				. ($aliasValue ? ' AND value_alias=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($aliasValue, 'tx_realurl_uniqalias' ) : ''), '', '', '1'
		);
		
		if (is_array($row)) {
			return $row['value_alias'];
		}
		return null;
	}

	/**
	 * translates an Id to an Alias
	 *
	 * @param array $cfg
	 * @param int $value
	 * @return string alias
	 */
	protected function id2alias($cfg, $value) {

		if (!$value) return 0;

		// if already an Alias Cached then return it
		$lookupResult = $this->lookUp_idToUniqAlias($cfg, $value, 0);
		if ($lookupResult){
			return $lookupResult;
		}

		// get Extension Config
		$conf = tx_fbmagento_tools::getExtConfig();

		// do Soap Call
		try {

			$soapClient = new tx_fbmagento_soapinterface($conf['url'], $conf['username'], $conf['password']);
			$soapClient->enableCache( isset($cfg['cache']) ? $cfg['cache'] : null);

			switch ($cfg['type']) {

				case 'idList':
					$result = $soapClient->call($cfg['table']);
					$newValue = $result[$value];
					break;

				default:
					$result = $soapClient->call($cfg['table'], array($value));
					$newValue = $result[$cfg['alias_field']];
					break;
			}
	
		} catch (Exception $e) {

			if ($this->getRealurlRef()->enableDevLog) {
				t3lib_div::devLog('Typogento Soap Error: ' . $e->getMessage(), 'realurl', -1);
			}
			return $value;
		}

		return $this->lookUp_newAlias($cfg, $newValue, $value, 0);
	}

	/**
	 * translates an Alias to an Id
	 *
	 * @param array $cfg
	 * @param string $value
	 * @return int Id
	 */
	protected function alias2id($cfg, $value) {

		$result = $this->lookUp_uniqAliasToId($cfg, $value);

		return $result;
	}

	/**
	 * Creates a new alias<->id relation in database lookup table.
	 *
	 * WARNING! This function is internal to RealURL. It is made public for
	 * backwards compatibility but its behavior and parameters may change as
	 * necessary for RealURL. No guaranties at all!
	 *
	 * @param array Configuration array of lookup table
	 * @param string Preferred new alias (final alias might be different if duplicates were found in the cache)
	 * @param integer ID associated with alias
	 * @param integer sys_language_uid to store with record
	 * @return string Final alias string
	 * @see lookUpTranslation()
	 * @internal
	 */
	protected function lookUp_newAlias($cfg, $newAliasValue, $idValue, $lang) {

		// Clean preferred alias
		$newAliasValue = $this->lookUp_cleanAlias($cfg, $newAliasValue);

		// If autoupdate is true we might be here even if an alias exists.
		// Therefore we check if that alias is the $newAliasValue and if so, 
		// we return that instead of making a new, unique one.
		if ($cfg['autoUpdate'] && $this->lookUp_idToUniqAlias($cfg, $idValue, $lang, $newAliasValue)) {
			return $newAliasValue;
		}

		// Now, go create a unique alias:
		$uniqueAlias = '';
		$counter = 0;
		$maxTry = 100;
		while ($counter < $maxTry) {

			// Suffix numbers if counter is larger than zero (in order to make unique alias):
			if ($counter > 0) {
				$test_newAliasValue = $newAliasValue . '-' . $counter;
			} else {
				$test_newAliasValue = $newAliasValue;
			}
			// If the test-alias did NOT exist, it must be unique and we break out:
			if (!$this->lookUp_uniqAliasToId($cfg, $test_newAliasValue, TRUE)) {
				$uniqueAlias = $test_newAliasValue;
				break;
			}
			// Otherwise, increment counter and test again...
			$counter++;
		}

		// if no unique alias was found in the process above, just suffix a hash string and assume that is unique...
		if (!$uniqueAlias) {
			$uniqueAlias = $newAliasValue .= '-' . t3lib_div::shortMD5(microtime());
		}

		// Insert the new id<->alias relation:
		$insertArray = array(
			'tstamp' => time(),
			'tablename' => $cfg['table'],
			'field_alias' => $cfg['alias_field'],
			'field_id' => $cfg['id_field'],
			'value_alias' => $uniqueAlias,
			'value_id' => $idValue,
			'lang' => $lang
		);

		// Checking that this alias hasn't been stored since we looked last time:
		if (($returnAlias = $this->lookUp_idToUniqAlias($cfg, $idValue, $lang, $uniqueAlias))) {
			// If we are here it is because another process managed to create this alias in the time between we looked the first time and now when we want to put it in database.
			$uniqueAlias = $returnAlias;
		} else {
			// Expire all other aliases:
			// Look for an alias based on ID:
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery(
				'tx_realurl_uniqalias',
				'value_id=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($idValue, 'tx_realurl_uniqalias')
				. 'AND field_alias=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($cfg['alias_field'], 'tx_realurl_uniqalias')
				. 'AND field_id=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($cfg['id_field'], 'tx_realurl_uniqalias')
				. 'AND tablename=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($cfg['table'], 'tx_realurl_uniqalias')
				. 'AND lang=' . intval($lang)
				. 'AND expire=0', array('expire' => time() + 24 * 3600 * ($cfg['expireDays'] ? $cfg['expireDays'] : 60))
			);

			// Store new alias:
			$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_realurl_uniqalias', $insertArray);
		}

		// Return new unique alias:
		return $uniqueAlias;
	}

	/**
	 * Clean up the alias
	 * (Almost the same function as encodeTitle() in class.tx_realurl_advanced.php)
	 *
	 * @param	array		Configuration array
	 * @param	string		Alias value to clean up
	 * @return	string		New alias value
	 * @see lookUpTranslation()
	 */
	public function lookUp_cleanAlias($cfg, $newAliasValue) {

		// Fetch character set:
		$charset = $GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'] ? $GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'] : $GLOBALS['TSFE']->defaultCharSet;
		$processedTitle = $newAliasValue;

		// Convert to lowercase:
		if ($cfg['useUniqueCache_conf']['strtolower']) {
			$processedTitle = $GLOBALS['TSFE']->csConvObj->conv_case($charset, $processedTitle, 'toLower');
		}

		// Convert some special tokens to the space character:
		$space = $cfg['useUniqueCache_conf']['spaceCharacter'] ? substr($cfg['useUniqueCache_conf']['spaceCharacter'], 0, 1) : '_';
		$processedTitle = strtr($processedTitle, ' -+_', $space . $space . $space . $space); // convert spaces

		// Convert extended letters to ascii equivalents:
		$processedTitle = $GLOBALS['TSFE']->csConvObj->specCharsToASCII($charset, $processedTitle);

		// Strip the rest
		if ($this->extConf['init']['enableAllUnicodeLetters']) {
			// Warning: slow!!!
			$processedTitle = preg_replace('/[^\p{L}0-9\\' . $space . ']/u', '', $processedTitle);
		} else {
			$processedTitle = preg_replace('/[^a-zA-Z0-9\\' . $space . ']/', '', $processedTitle);
		}

		$processedTitle = preg_replace('/\\' . $space . '{2,}/', $space, $processedTitle); // Convert multiple 'spaces' to a single one
		$processedTitle = trim($processedTitle, $space);

		if ($cfg['useUniqueCache_conf']['encodeTitle_userProc']) {
			$encodingConfiguration = array(
				'strtolower' => $cfg['useUniqueCache_conf']['strtolower'],
				'spaceCharacter' => $cfg['useUniqueCache_conf']['spaceCharacter']
			);

			$params = array(
				'pObj' => &$this,
				'title' => $newAliasValue,
				'processedTitle' => $processedTitle,
				'encodingConfiguration' => $encodingConfiguration
			);

			$processedTitle = t3lib_div::callUserFunction($cfg['useUniqueCache_conf']['encodeTitle_userProc'], $params, $this);
		}

		// Return value:
		return $processedTitle;
	}

	/**
	 * Generates additional RealURL configuration and merges it with provided configuration
	 *
	 * @param array $paramsDefault configuration
	 * @param tx_realurl_autoconfgen $pObjParent object
	 * @return array Updated configuration
	 */
	public function addMagentoConfig($params, &$pObj) {

		return array_merge_recursive($params['config'], array(
			'postVarSets' => array(
				'_DEFAULT' => array(
					'shoparticle' => array(
						array(
							'GETvar' => 'tx_fbmagento[shop][s]',
						),
					),
					'shop' => array(
						array(
							'GETvar' => 'tx_fbmagento[shop][route]',
	
						),
						array(
							'GETvar' => 'tx_fbmagento[shop][controller]',
						),
						array(
							'GETvar' => 'tx_fbmagento[shop][action]',
						),
						array(
							'GETvar' => 'tx_fbmagento[shop][id]',
							'userFunc' => 'EXT:fbmagento/lib/class.tx_fbmagento_realurl.php:&tx_fbmagento_realurl->idRewrite',
						),
						array(
							'GETvar' => 'tx_fbmagento[shop][category]',
							'userFunc' => 'EXT:fbmagento/lib/class.tx_fbmagento_realurl.php:&tx_fbmagento_realurl->categoryRewrite',
						),
						array (
							'GETvar' => 'tx_fbmagento[shop][product]',
						),
					),
					'shoppage' => array(
						array(
							'GETvar' => 'tx_fbmagento[shop][p]',
						),
						array(
							'GETvar' => 'tx_fbmagento[shop][order]',
						),
						array(
							'GETvar' => 'tx_fbmagento[shop][dir]',
						)
					)
				)
			)
		));
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fb_magento/lib/class.tx_fbmagento_realurl.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fb_magento/lib/class.tx_fbmagento_realurl.php']);
}

?>
