<?php



class Flagbit_Typo3connect_Model_Customer extends Mage_Customer_Model_Customer {

	
	/**
	 * create or update an TYPO3 Frontend User
	 *
	 */
	public function _afterSave(){
		
		parent::_afterSave();
		
		if (! Mage::getSingleton ( 'Flagbit_Typo3connect/Core' )->isEnabled ()) return;
		

		$fields = array(
			'username' => $this->getData('email'),
			'name' => $this->getData('lastname'),
			'firstname' => $this->getData('firstname'),
			'email' => $this->getData('email'),
			'password' => $this->getData('password'),
			'usergroup' => Mage::helper('Flagbit_Typo3connect')->getConfigData('fe_user_group_uid'),
			'pid' => Mage::helper('Flagbit_Typo3connect')->getConfigData('fe_user_pid'),
			'tx_fbmagento_id' => $this->getId(),
		);
		
		$feUsers = Mage::getSingleton('Flagbit_Typo3connect/Typo3_FeUsers');
		$this->load($this->getId());
		
		if($this->getTypo3_uid()){
			$feUsers->setId($this->getTypo3_uid());
		}		
		
		foreach($fields as $key => $value){
			$feUsers->setData($key, $value);
		}
		$feUsers->save();

    	$this->setData('typo3_uid', $feUsers->getId());
    	$this->getResource()->saveAttribute($this, 'typo3_uid');
	
	}
	
 

}

?>