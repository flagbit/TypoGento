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
 * TypoGento Layout Model
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class Flagbit_Typo3connect_Model_Layout extends Mage_Core_Model_Layout {
	
	/**
	 * Class Constuctor
	 *
	 * @param array $data
	 */
	public function __construct($data = array()) {
		parent::__construct ( $data );
	}
	
	/**
	 * Get all blocks marked for output
	 *
	 * @return string
	 */
	public function getOutput() {
		
		if (! Mage::getSingleton ( 'Flagbit_Typo3connect/Core' )->isEnabled ()) {
			return parent::getOutput ();
		}
		
		$out = '';
		
		foreach ( $this->_blocks as $key => $block ) {
			Mage::getSingleton ( 'Flagbit_Typo3connect/Core' )->setBlock ( $key, $block );
		}
		
		//return parent::getOutput();

		if (! empty ( $this->_output )) {
			foreach ( $this->_output as $callback ) {
				$out .= $this->getBlock ( $callback [0] )->$callback [1] ();
			}
		}
		
		return $out;
	}

}

?>