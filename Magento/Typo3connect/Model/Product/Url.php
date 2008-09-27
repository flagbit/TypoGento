<?php

class Flagbit_Typo3connect_Model_Product_Url extends Mage_Catalog_Model_Product_Url {
	
	/**
	 * Get product url
	 *
	 * @param  Mage_Catalog_Model_Product $product
	 * @return string
	 */
	public function getProductUrl($product) {
		
		if(!Mage::getSingleton('Flagbit_Typo3connect/Core')->isEnabled()){
			return parent::getProductUrl($product);	
		}		

		$queryParams = '';

		Varien_Profiler::start ( 'REGULAR: ' . __METHOD__ );
		
		$url = $this->getUrlInstance ()->getUrl ( 'catalog/product/view', array ('id' => $product->getId (), 's' => $product->getUrlKey (), 'category' => $product->getCategoryId () ) ) . $queryParams;
		Varien_Profiler::stop ( 'REGULAR: ' . __METHOD__ );
		
		return $url;
	}
}