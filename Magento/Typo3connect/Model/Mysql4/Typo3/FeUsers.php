<?php


class Flagbit_Typo3connect_Model_Mysql4_Typo3_FeUsers extends Mage_Core_Model_Mysql4_Abstract {

    protected function _construct()
    {
        $this->_init('Flagbit_Typo3connect/typo3_feusers', 'uid');
        

    }	
	
    /**
     * Get an TYPO3 fe_user
     *
     * @param   int unique ID
     * @return  array
     */
    public function getUserById($id)
    {
        $read = $this->_getReadAdapter();
        $select = $read->select();

        $select->from(array('main_table'=>$this->getMainTable()))
            ->where($this->getIdFieldName().' = ?', $id)
            ->limit(1);

        return $read->fetchRow($select);
    }
    
}

?>