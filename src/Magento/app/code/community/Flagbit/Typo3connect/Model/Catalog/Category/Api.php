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
 * TypoGento Api Category Catalog Model
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class Flagbit_Typo3connect_Model_Catalog_Category_Api extends Mage_Catalog_Model_Api_Resource
{

	
    /**
     * Retrieve category urlKeys
     *
     * @param string|int $store
     * @return array
     */
    public function urlkeys($store = null)
    {
 	
        $collection = Mage::getModel('catalog/category')->getCollection()
            ->setStoreId($this->_getStoreId($store))
            ->addAttributeToSelect('url_key')->load();

        $collectionArray = array();

        foreach ($collection as $category) {
        	$collectionArray[$category->getId()]= $category->getUrlKey();
        }
           
		return $collectionArray;
    }
} 