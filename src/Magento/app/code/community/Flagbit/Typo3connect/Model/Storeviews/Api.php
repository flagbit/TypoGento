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
 * TypoGento Api Storeview Model
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class Flagbit_Typo3connect_Model_Storeviews_Api extends Mage_Api_Model_Resource_Abstract
{
    /**
     * Retrieve storeview list
     *
     * @return array
     */
    public function items()
    {

        $storeviews = Mage::getModel('core/store')->getCollection();
        
        $res = array();
        $additional['value'] = 'code';
        $additional['label'] = 'name';

        foreach ($storeviews as $item) {
            foreach ($additional as $code => $field) {
                $data[$code] = $item->getData($field);
            }
        	$res[] = $data;
        }
        return $res;        
    }
} 