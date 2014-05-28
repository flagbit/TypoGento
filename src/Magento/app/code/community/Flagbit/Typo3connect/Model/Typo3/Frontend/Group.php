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
 * TypoGento Group Model
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class Flagbit_Typo3connect_Model_Typo3_Frontend_Group extends Mage_Core_Model_Abstract {

	/**
	 * Class Constructor
	 *
	 */
    public function _construct()
    {
    
        $this->_init('Flagbit_Typo3connect/typo3_frontend_group');
    }	
    
    /**
     * get fe_group by uid
     *
     * @param int $id
     * @return array Group Data
     */
    public function getGroupById($id){
    	return $this->_getResource()->getGroupById($id);
    }

}
