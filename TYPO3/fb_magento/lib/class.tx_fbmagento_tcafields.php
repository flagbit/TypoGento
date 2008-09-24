<?php

require_once(t3lib_extmgm::extPath('fb_magento').'lib/class.tx_fbmagento_soapinterface.php');
require_once(t3lib_extmgm::extPath('fb_magento').'lib/class.tx_fbmagento_tools.php');

class tx_fbmagento_tcafields {

	public function itemsProcFunc_products(&$params,&$pObj){
		
		$conf = tx_fbmagento_tools::getExtConfig();
		
		$soapClient = new tx_fbmagento_soapinterface($conf['url'], $conf['username'], $conf['password']);
		$products = $soapClient->catalog_product()->list();
		
		foreach ((array) $products as $product){
			$params['items'][]=Array($product['name'].' - '.$product['sku'], $product['product_id']);
		}

	}
	
}

?>