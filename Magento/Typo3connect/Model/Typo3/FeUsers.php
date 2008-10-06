<?php


class Flagbit_Typo3connect_Model_Typo3_FeUsers extends Mage_Core_Model_Abstract {

    public function _construct()
    {
    
        $this->_init('Flagbit_Typo3connect/Typo3_FeUsers');
    }	
    
    public function getUserById($id){
    	//$this->_resourcePrefix = 'typo3connect';

    	return $this->_getResource()->getUserById($id);
    	
    }
    


	
}

?>