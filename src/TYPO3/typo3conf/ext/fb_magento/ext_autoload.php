<?php

$extensionPath = t3lib_extMgm::extPath('fb_magento');
return array(
	'tx_fbmagento_pi1' => $extensionPath.'pi1/class.tx_fbmagento_pi1.php',
	'tx_fbmagento_auth_sv1' => $extensionPath.'lib/class.tx_fbmagento_auth_sv1.php',
	'tx_fbmagento_autoloader' => $extensionPath.'lib/class.tx_fbmagento_autoloader.php',
	'tx_fbmagento_cache' => $extensionPath.'lib/class.tx_fbmagento_cache.php',
	'tx_fbmagento_hookobserver' => $extensionPath.'lib/class.tx_fbmagento_hookobserver.php',
	'tx_fbmagento_interface' => $extensionPath.'lib/class.tx_fbmagento_interface.php',
	'tx_fbmagento_navigation' => $extensionPath.'lib/class.tx_fbmagento_navigation.php',
	'tx_fbmagento_realurl' => $extensionPath.'lib/class.tx_fbmagento_realurl.php',
	'tx_fbmagento_soapinterface' => $extensionPath.'lib/class.tx_fbmagento_soapinterface.php',
	'tx_fbmagento_tcafields' => $extensionPath.'lib/class.tx_fbmagento_tcafields.php',
	'tx_fbmagento_tools' => $extensionPath.'lib/class.tx_fbmagento_tools.php'
);

?>