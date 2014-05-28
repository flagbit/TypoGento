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
 * TypoGento Product Url Model
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class Flagbit_Typo3connect_Model_Product_Url extends Mage_Catalog_Model_Product_Url {
	
	/**
	 * Get product url
	 *
	 * @param  Mage_Catalog_Model_Product $product
	 * @param bool $useSid
	 * @return string
	 */
	public function getProductUrl($product, $useSid = true) {
		
		if(!Mage::getSingleton('Flagbit_Typo3connect/Core')->isEnabled()){
			return parent::getProductUrl($product, $useSid);	
		}		

		$queryParams = '';

		Varien_Profiler::start ( 'REGULAR: ' . __METHOD__ );
		
		$url = $this->getUrlInstance ()->getUrl ( 'catalog/product/view', array ('id' => $product->getId (), 's' => $product->getUrlKey (), 'category' => $product->getCategoryId () ) ) . $queryParams;
		Varien_Profiler::stop ( 'REGULAR: ' . __METHOD__ );
		
		return $url;
	}
}