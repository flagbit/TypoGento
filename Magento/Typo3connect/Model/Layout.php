<?php

class Flagbit_Typo3connect_Model_Layout extends Mage_Core_Model_Layout {
	
	/**
	 * Enter description here...
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

		if (! empty ( $this->_output )) {
			foreach ( $this->_output as $callback ) {
				$out .= $this->getBlock ( $callback [0] )->$callback [1] ();
			}
		}
		
		return $out;
	}

}

?>