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
 * @version $Id$
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
require_once(t3lib_extmgm::extPath('fb_magento').'lib/class.tx_fbmagento_soapinterface.php');
require_once(t3lib_extmgm::extPath('fb_magento').'lib/class.tx_fbmagento_tools.php');

class tx_fbmagento_tcafields {

	/**
	 * generates an Productlist as Array for TCA Select fields
	 *
	 * @param array $params
	 * @param object $pObj
	 */
	public function itemsProcFunc_products(&$params,&$pObj){
		
		$conf = tx_fbmagento_tools::getExtConfig();
		
		try {
			
			$soapClient = new tx_fbmagento_soapinterface($conf['url'], $conf['username'], $conf['password']);
			$products = $soapClient->catalog_product()->list();
			
		}catch (Exception $e){
			tx_fbmagento_tools::displayError('SOAP API Error: '.$e->getMessage());
		}		
		
		foreach ((array) $products as $product){
			$params['items'][]=Array($product['name'].' - '.$product['sku'], $product['product_id']);
		}
	}

	/**
	 * generates an Grouplist as Array for TCA Select fields
	 *
	 * @param array $params
	 * @param object $pObj
	 */
	public function itemsProcFunc_usergroups(&$params,&$pObj){
		
		$conf = tx_fbmagento_tools::getExtConfig();
		
		try {
			
			$soapClient = new tx_fbmagento_soapinterface($conf['url'], $conf['username'], $conf['password']);
			$roles = $soapClient->typo3connect_admin_roles()->list();
			
		}catch (Exception $e){
			tx_fbmagento_tools::displayError('SOAP API Error: '.$e->getMessage());
		}		
		
		foreach ((array) $roles as $role){
			$params['items'][]=Array($role['label'], $role['value']);
		}
	}
	
	/**
	 * generates an Storeviewlist as Array for TCA Select fields
	 *
	 * @param array $params
	 * @param object $pObj
	 */
	public function itemsProcFunc_languages(&$params,&$pObj){
		
		$conf = tx_fbmagento_tools::getExtConfig();

		try {
			
			$soapClient = new tx_fbmagento_soapinterface($conf['url'], $conf['username'], $conf['password']);
			$storeviews = $soapClient->typo3connect_storeviews()->list();
			
		}catch (Exception $e){
			tx_fbmagento_tools::displayError('SOAP API Error: '.$e->getMessage());
		}		
				
		foreach ((array) $storeviews as $storeview){
			$params['items'][]=Array($storeview['label'], $storeview['value']);
		}
	}	
	

	/**
	 * generates an Category as Array for TCA Select fields
	 *
	 * @param array $params
	 * @param object $pObj
	 */	
	public function itemsProcFunc_categories(&$params,&$pObj){
		
		$conf = tx_fbmagento_tools::getExtConfig();
		
		try {
			
			$soapClient = new tx_fbmagento_soapinterface($conf['url'], $conf['username'], $conf['password']);
			$categories = $soapClient->catalog_category()->tree();
			
		}catch (Exception $e){
			tx_fbmagento_tools::displayError('SOAP API Error: '.$e->getMessage());
		}

		$this->getCategoryItems($params['items'], array($categories));
	}
	
	/**
	 * generates an recursive list of Categories
	 *
	 * @param array $items
	 * @param array $categories
	 */
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