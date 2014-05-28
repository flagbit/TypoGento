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
 * TypoGento Catalog Url Model
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class Flagbit_Typo3connect_Model_Catalog_Url extends Mage_Catalog_Model_Url
{
    /**
     * I'm not sure what exactly needs to be done here, will debug later. 
     * 
     * 
     * Generate either id path, request path or target path for product and/or category
     *
     * For generating id or system path, either product or category is required
     * For generating request path - category is required
     * $parentPath used only for generating category path
     *
     * @param string $type
     * @param Varien_Object $product
     * @param Varien_Object $category
     * @param string $parentPath
     * @return string
     * @throws Mage_Core_Exception
     * @todo Test category browsing in typo3
     */
    public function generatePath($type = 'target', $product = null, $category = null, $parentPath = null)
    {
		
		if(!Mage::getSingleton('Flagbit_Typo3connect/Core')->isEnabled()){
			return parent::generatePath($type, $product, $category, $parentPath);
		}	
		
		return parent::generatePath($type, $product, $category, $parentPath);
    }
}