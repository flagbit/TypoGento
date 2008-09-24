<?php

class tx_fbmagento_main {
	
	public function getExtConfig(){
		return unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['fb_magento']);
	}
	
}

?>