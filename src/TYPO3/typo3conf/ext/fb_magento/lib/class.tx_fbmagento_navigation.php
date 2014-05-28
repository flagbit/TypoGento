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
 * TypoGento navigation
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class user_tx_fbmagento_navigation extends tx_fbmagento_navigation {
}

class tx_fbmagento_navigation {

	protected $_categoryTree = array();
	protected $_get = null;

	public function __construct() {
		if(!is_array($this->_get)) {
			$this->_get = t3lib_div :: _GET('tx_fbmagento');
		}
	}

	/**
	 * Returns current Category if available
	 *
	 * @return Mage_Catalog_Model_Category
	 */
	public function getCurrentCategory() {
		if (Mage::getSingleton('catalog/layer')) {
			return Mage::getSingleton('catalog/layer')->getCurrentCategory();
		}
		return false;
	}

	/**
	 * Checking activity of category
	 *
	 * @param Varien_Object $category
	 * @return boolean
	 */
	public function isCategoryActive($category) {
		if ($this->getCurrentCategory()) {
			return in_array($category->getId(), $this->getCurrentCategory()->getPathIds());
		}
		return false;
	}

	protected function _getCategoryInstance() {
		if (is_null($this->_categoryInstance)) {
			$this->_categoryInstance = Mage::getModel('catalog/category');
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
			$url = $category->getUrl();
		} else {
			$url = $this->_getCategoryInstance()->setData($category->getData())->getUrl();
		}
		return $url;
	}

	/**
	 * Function clears all subelements. This is needed for clear error with mix up pages and categories 
	 *
	 * @param array $menuArr: Array with menu item
	 * @param array $conf: TSconfig, not used
	 * @return array return the cleaned menu item
	 */
	function clear($menuArr, $conf) {
		while (list(, $item) = each($menuArr)) {
			if ($item['DO_NOT_RENDER'] == '1') {
				$menuArr = array();
			}
		}
		return $menuArr;
	}

	/**
	 * creates HMENU Items Array
	 *
	 * @param int $category
	 * @param int $level
	 * @param boolan $last
	 * @return array
	 */
	protected function createMenuArrayItem($category, $level = 0, $last = false, $parent = null) {
		$menuArray = array ();

		if (!$category->getIsActive()) {
			return;
		}

		$children = $category->getChildren();
		$hasChildren = $children && $children->count();

		$menuArray['title'] = $category->getName();

		$params = array(
			'id' => $category->getId(),
			'route' => 'catalog',
			'controller' => 'category',
			'action' =>'view'
		);

		$menuArray['_OVERRIDE_HREF'] = $GLOBALS['TSFE']->cObj->getTypoLink_URL($this->conf['pid'], array('tx_fbmagento' => array('shop' => $params)));;

		if ($category->getAct()) {
			$menuArray['ITEM_STATE'] = 'ACT';

			if ($hasChildren) {
				$j = 0;
				foreach ($children as $child) {
					if ($child->getIsActive()) {
						$menuArray['_SUB_MENU'][] = $this->createMenuArrayItem($child, $level + 1, ++ $j >= 0);
					}
				}
			} else {
				$menuArray ['_SUB_MENU'][] = array ('DO_NOT_RENDER' => 1);
			}
		} else {
			$menuArray['_SUB_MENU'][] = array('DO_NOT_RENDER' => 1);
		}

		return $menuArray;
	}

	/**
	 * generates HMENU Array
	 *
	 * @param string $content
	 * @param array $conf
	 * @return array
	 */
	public function categories($content, $conf) {

		$this->emConf = tx_fbmagento_tools::getExtConfig();
		$this->conf = $conf;
		$this->mage = t3lib_div::makeInstance('tx_fbmagento_interface', $this->emConf );

		$categories = $this->getStoreCategories($this->conf['startcategory']);

		$menu = array();
		foreach ($categories as $category) {
			$item = $this->createMenuArrayItem($category);
			if (!$item) {
				continue;
			}

			$menu[] = $item;
		}

		return $menu;
	}

	/**
	 * Retrieve current store categories
	 *
	 * @param boolean|string $sorted
	 * @param boolean $asCollection
	 * @return Varien_Data_Tree_Node_Collection|Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Collection|array
	 */
	public function getStoreCategories($parent=null) {
		if (!$parent) {
			$parent = Mage::app()->getStore()->getRootCategoryId();
		}

		// Check if parent node of the store still exists
		$category = Mage::getModel('catalog/category');

		/*@var $category Mage_Catalog_Model_Category*/
		if (!$category->checkId($parent)) {
			return array();
		}

		$recursionLevel = max(0, (int) Mage::app()->getStore()->getConfig('catalog/navigation/max_depth'));

		$tree = $category->getTreeModel();
		/*@var $tree Mage_Catalog_Model_Resource_Eav_Mysql4_Category_Tree*/

		$nodes = $tree->loadNode($parent)
			->loadChildren($recursionLevel)
			->getChildren();

		$tree->addCollectionData(null, false, $parent, true, true);

		$this->_parseNodes($nodes);
		return $nodes;
	}

	/**
	 * Simple parse function that is thought for menu status changes
	 * 
	 * @param array $nodes array with all menu item nodes
	 * @return boolean True if current category is active
	 */
	protected function _parseNodes($nodes) {
		foreach($nodes as $node) {
			if(isset($this->_get['shop']['category'])){
				if($node->getId() == intval($this->_get['shop']['category'])
					|| ($node->getChildren() && $this->_parseNodes($node->getChildren()))) {
							$node->setAct(true);
							return true;
				}
			} else {
				if($node->getId() == intval($this->_get['shop']['id'])
					|| ($node->getChildren() && $this->_parseNodes($node->getChildren()))) {
						$node->setAct(true);
						return true;
				}
			}
		}
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fb_magento/lib/class.tx_fbmagento_navigation.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fb_magento/lib/class.tx_fbmagento_navigation.php']);
}

?>
