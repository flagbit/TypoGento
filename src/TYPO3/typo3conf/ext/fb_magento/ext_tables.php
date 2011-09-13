<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key';
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key, pages, recursive';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform';
t3lib_extMgm::addPlugin(array('LLL:EXT:fb_magento/locallang_db.xml:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');
t3lib_extMgm::addPiFlexFormValue($_EXTKEY .'_pi1', 'FILE:EXT:fb_magento/pi1/flexform.xml');
t3lib_extMgm::addStaticFile($_EXTKEY,"pi1/static/","TypoGento");

if (TYPO3_MODE=="BE")	$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_fbmagento_pi1_wizicon"] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_fbmagento_pi1_wizicon.php';

$tempColumns = Array (
	"firstname" => Array (
		"exclude" => 0,
		"label" => "LLL:EXT:fb_magento/locallang_db.xml:fe_users.tx_fbmagento_firstname",
		"config" => Array (
			"type" => "input",
			"size" => "30",
		)
	),
	"tx_fbmagento_id" => Array (
		"exclude" => 1,
		"label" => "LLL:EXT:fb_magento/locallang_db.xml:fe_users.tx_fbmagento_id",
		"config" => Array (
			"type" => "input",
			"size" => "30",
		)
	),
);

t3lib_div::loadTCA("fe_users");
t3lib_extMgm::addTCAcolumns("fe_users",$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes("fe_users","firstname;;;;1-1-1");
t3lib_extMgm::addToAllTCAtypes("fe_users","tx_fbmagento_id;;;;1-1-1");

// add store mapping
$tempColumns = Array (
	"tx_fbmagento_store" => Array(
		"exclude" => 0,
		"label" => "LLL:EXT:fb_magento/locallang_db.xml:sys_language.tx_fbmagento_store",
		"config" => Array (
			"type" => "select",
			"itemsProcFunc" => "EXT:fb_magento/lib/class.tx_fbmagento_tcafields.php:tx_fbmagento_tcafields->itemsProcFunc_languages",
			"maxitems" => 1
		)
	)
);

t3lib_div::loadTCA("sys_language");
t3lib_extMgm::addTCAcolumns("sys_language",$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes("sys_language","tx_fbmagento_store;;;;1-1-1");

// add group mapping
$tempColumns = Array (
	"tx_fbmagento_group" => Array(
		"exclude" => 0,
		"label" => "LLL:EXT:fb_magento/locallang_db.xml:be_users.tx_fbmagento_group",
		"config" => Array (
			"type" => "select",
			"items" => array(
				array('LLL:EXT:fb_magento/locallang_db.xml:be_users.tx_fbmagento_group.0', '')
			),
			"itemsProcFunc" => "EXT:fb_magento/lib/class.tx_fbmagento_tcafields.php:tx_fbmagento_tcafields->itemsProcFunc_usergroups",
			"maxitems" => 1
		)
	)
);

t3lib_div::loadTCA("be_users");
t3lib_extMgm::addTCAcolumns("be_users",$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes("be_users","tx_fbmagento_group;;;;1-1-1");

if (TYPO3_MODE=="BE" /* && !empty($GLOBALS['BE_USER']->user['tx_fbmagento_group'])*/) {

		// add module after 'Web'
	if (!isset($TBE_MODULES['txfbmagentoMgroup'])) {
		$temp_TBE_MODULES = array();
		foreach ($TBE_MODULES as $key => $val) {
			$temp_TBE_MODULES[$key] = $val;
			if ($key == 'web') {
				$temp_TBE_MODULES['txfbmagentoMgroup'] = $val;
			}
		}
		$TBE_MODULES = $temp_TBE_MODULES;

	}

	// add group module
	t3lib_extMgm::addModule('txfbmagentoMgroup','','',t3lib_extmgm::extPath($_EXTKEY).'mod_group/');

	// add admin module
	t3lib_extMgm::addModule('txfbmagentoMgroup','txfbmagentoMadmin','',t3lib_extmgm::extPath($_EXTKEY).'mod_admin/');

}

// register Magento autoloader for backend
//TODO calling a hook direct before session is started would be nicer. But there is none.
t3lib_div::makeInstance('tx_fbmagento_autoloader', tx_fbmagento_tools::getExtConfig());

?>