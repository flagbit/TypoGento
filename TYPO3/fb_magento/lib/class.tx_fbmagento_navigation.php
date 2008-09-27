<?php

require_once (t3lib_extmgm::extPath ( 'fb_magento' ) . 'lib/class.tx_fbmagento_tools.php');
require_once (t3lib_extmgm::extPath ( 'fb_magento' ) . 'lib/class.tx_fbmagento_interface.php');

class user_tx_fbmagento_navigation extends tx_fbmagento_navigation {

}

class tx_fbmagento_navigation {
	
	/**
	 * Enter description here...
	 *
	 * @return Mage_Catalog_Model_Category
	 */
	public function getCurrentCategory() {
		if (Mage::getSingleton ( 'catalog/layer' )) {
			return Mage::getSingleton ( 'catalog/layer' )->getCurrentCategory ();
		}
		return false;
	}
	
	/**
	 * Checkin activity of category
	 *
	 * @param   Varien_Object $category
	 * @return  bool
	 */
	public function isCategoryActive($category) {
		if ($this->getCurrentCategory ()) {
			return in_array ( $category->getId (), $this->getCurrentCategory ()->getPathIds () );
		}
		return false;
	}
	
	protected function _getCategoryInstance() {
		if (is_null ( $this->_categoryInstance )) {
			$this->_categoryInstance = Mage::getModel ( 'catalog/category' );
		}
		return $this->_categoryInstance;
	}
	
	/**
	 * Get url for category data
	 *
	 * @param Mage_Catalog_Model_Category $category
	 * @return string
	 */
	public function getCategoryUrl($category) {
		if ($category instanceof Mage_Catalog_Model_Category) {
			$url = $category->getUrl ();
		} else {
			$url = $this->_getCategoryInstance ()->setData ( $category->getData () )->getUrl ();
		}
		return $url;
	}
	
	/**
	 * Function clears all subelements. This is needed for clear error with mix up pages and categories 
	 *
	 * @param	array		$menuArr: Array with menu item
	 * @param	array		$conf: TSconfig, not used
	 * @return	array		return the cleaned menu item
	 */
	function clear($menuArr, $conf) {
		while ( list ( , $item ) = each ( $menuArr ) ) {
			if ($item ['DO_NOT_RENDER'] == '1') {
				$menuArr = array ();
			}
		}
		return $menuArr;
	}
	
	protected function createMenuArrayItem($category, $level = 0, $last = false) {
		$menuArray = array ();
		
		if (! $category->getIsActive ()) {
			return;
		}
		
		$children = $category->getChildren ();
		$hasChildren = $children && $children->count ();
		
		$menuArray ['title'] = $category->getName ();
		
		$params = array(
			'id' => $category->getId(),
			'route' => 'catalog',
			'controller' => 'category',
			'action' =>'view'
		);
		$menuArray ['_OVERRIDE_HREF'] = $GLOBALS['TSFE']->cObj->getTypoLink_URL($this->conf['pid'], array('tx_fbmagento' => array('shop' => $params)));;
		//$menuArray['ITEM_STATE'] = 'ACT';
		

		if ($hasChildren) {
			$j = 0;
			foreach ( $children as $child ) {
				if ($child->getIsActive ()) {
					$menuArray ['_SUB_MENU'] [] = $this->createMenuArrayItem ( $child, $level + 1, ++ $j >= 0 );
				}
			}
		} else {
			$menuArray ['_SUB_MENU'] [] = array ('DO_NOT_RENDER' => 1 );
		}
		
		return $menuArray;
	}
	
	function categories($content, $conf) {
		
		$this->emConf = tx_fbmagento_tools::getExtConfig ();
		$this->conf = $conf;
		$mage = tx_fbmagento_interface::getInstance ( $this->emConf );
		
		$helper = Mage::helper ( 'catalog/category' );
		$categories = $helper->getStoreCategories ();
		
		$menu = array ();
		foreach ( $categories as $category ) {
			$item = $this->createMenuArrayItem ( $category );
			if (! $item)
				continue;
			$menu [] = $item;
		}
		
		#print_r($menu);
		return $menu;
	
	}

}

?>