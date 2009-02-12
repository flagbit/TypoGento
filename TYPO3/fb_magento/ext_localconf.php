<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

  ## Extending TypoScript from static template uid=43 to set up userdefined tag:
t3lib_extMgm::addTypoScript($_EXTKEY,'editorcfg','
	tt_content.CSS_editor.ch.tx_fbmagento_pi1 = < plugin.tx_fbmagento_pi1.CSS_editor
',43);

// RealURL autoconfiguration
if($_EXTCONF['realurl']){
	require_once(t3lib_extmgm::extPath('fb_magento').'lib/class.tx_fbmagento_realurl.php');
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/realurl/class.tx_realurl_autoconfgen.php']['extensionConfiguration']['fbmagento'] = 'EXT:fbmagento/lib/class.tx_fbmagento_realurl.php:tx_fbmagento_realurl->addMagentoConfig';
}

t3lib_extMgm::addPItoST43($_EXTKEY,'pi1/class.tx_fbmagento_pi1.php','_pi1','list_type',0);

/**
 * Check $_GET Var Values
 *
 * @param string key|value $param
 * @return boolan
 */
function tx_fbmagento_getvars($param) {
	list($key, $value) = explode('|', $param);
	
	$pi_getVars = t3lib_div::_GET('tx_fbmagento');
	return ($pi_getVars['shop'][$key] == $value ? true : false);
}


?>