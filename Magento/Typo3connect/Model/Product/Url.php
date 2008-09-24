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
		//        $store = Mage::app()->getStore();
		//        if ($store->getId() && Mage::getStoreConfig(Mage_Core_Model_Url::XML_PATH_STORE_IN_URL)) {
		//            $queryParams = '?store='.$store->getCode();
		//        }
		

		if ($product->hasData ( 'request_path' ) && $product->getRequestPath () != '') {
			$url = $this->getUrlInstance ()->getBaseUrl () . $product->getRequestPath () . $queryParams;
			return $url;
		}
		/*
		Varien_Profiler::start ( 'REWRITE: ' . __METHOD__ );
		$rewrite = $this->getUrlRewrite ();
		if ($product->getStoreId ()) {
			$rewrite->setStoreId ( $product->getStoreId () );
		}
		
		$idPath = $idPathProduct = 'product/' . $product->getId ();
		if ($product->getCategoryId () && ! $product->getDoNotUseCategoryId () && Mage::getStoreConfig ( 'catalog/seo/product_use_categories' )) {
			$idPath .= '/' . $product->getCategoryId ();
		}
		
		$rewrite->loadByIdPath ( $idPath );
		
		if ($rewrite->getId ()) {
			$url = $this->getUrlInstance ()->getBaseUrl () . $rewrite->getRequestPath () . $queryParams;
			
			Varien_Profiler::stop ( 'REWRITE: ' . __METHOD__ );
			return $url;
		}
		//        else {
		//            print $idPathProduct;
		//            $rewrite->loadByIdPath($idPathProduct);
		//            if ($rewrite->getId()) {
		//                $url = $this->getUrlInstance()->getBaseUrl().$rewrite->getRequestPath().$queryParams;
		//                Varien_Profiler::stop('REWRITE: '.__METHOD__);
		//                return $url;
		//            }
		//        }
		Varien_Profiler::stop ( 'REWRITE: ' . __METHOD__ );
		*/
		Varien_Profiler::start ( 'REGULAR: ' . __METHOD__ );
		
		$url = $this->getUrlInstance ()->getUrl ( 'catalog/product/view', array ('id' => $product->getId (), 's' => $product->getUrlKey (), 'category' => $product->getCategoryId () ) ) . $queryParams;
		Varien_Profiler::stop ( 'REGULAR: ' . __METHOD__ );
		
		return $url;
	}
}