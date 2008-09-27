<?php


require_once (t3lib_extmgm::extPath ( 'fb_magento' ) . 'lib/class.tx_fbmagento_tools.php');
require_once (t3lib_extmgm::extPath ( 'fb_magento' ) . 'lib/class.tx_fbmagento_interface.php');

class user_tx_fbmagento_tsfunc extends tx_fbmagento_tsfunc {

}

class tx_fbmagento_tsfunc {

	public function getBlock($content, $conf){

		if(!$conf['name']) return '';		
		$this->emConf = tx_fbmagento_tools::getExtConfig ();
		$this->conf = $conf;
		$mage = tx_fbmagento_interface::getInstance ( $this->emConf );		
		
		return $mage->getContent($conf['name']);
	}
	
	
}

?>