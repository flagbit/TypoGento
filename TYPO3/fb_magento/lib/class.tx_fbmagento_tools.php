<?php

class tx_fbmagento_tools {
	
	public static function getExtConfig(){
		return unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['fb_magento']);
	}
	
}

?>