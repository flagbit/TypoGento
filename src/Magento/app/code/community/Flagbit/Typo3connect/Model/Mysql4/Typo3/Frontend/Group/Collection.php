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
 * TypoGento Login Helper
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */

class Flagbit_Typo3connect_Model_Mysql4_Typo3_Frontend_Group_Collection  extends Mage_Core_Model_Mysql4_Collection_Abstract
{

	
	protected function _construct()
    {
        $this->_init('Flagbit_Typo3connect/typo3_frontend_group');

    }
    
    
    public function toOptionArray()
    {
        return $this->_toOptionArray('uid', 'title');
    }       
    
 }