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

	public function itemsProcFunc_categories(&$params,&$pObj){
		
		$conf = tx_fbmagento_tools::getExtConfig();
		
		$soapClient = new tx_fbmagento_soapinterface($conf['url'], $conf['username'], $conf['password']);
		$categories = $soapClient->catalog_category()->tree();

		$this->getCategoryItems($params['items'], array($categories));
	}
	
	protected function getCategoryItems(&$items, $categories){
		
		foreach ($categories as $category){
			$items[] = array(str_repeat('-',$category['level']*2).$category['name'], $category['category_id']);
			if(is_array($category['children'])){
				$this->getCategoryItems($items, $category['children'], $category['level']);
			}
		}
		
	}
	
}

?>