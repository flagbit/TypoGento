<?php
/*                                                                        *
 * This script is part of the TypoGento project                           *
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

require_once(t3lib_extmgm::extPath('sv').'class.tx_sv_auth.php');

/**
 * TypoGento Customer Auth
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class tx_fbmagento_auth_sv1 extends tx_sv_auth {

	protected $_mageCustomer = null;
	protected $_feUserCache = array();

	/**
	 * Magento single sign on
	 *
	 * @return mixed user array or false
	 */
	public function getUser() {
		// get Extension Config
		$this->emConf = tx_fbmagento_tools::getExtConfig();

		// get an Magento Instance
		$this->mage = t3lib_div::makeInstance('tx_fbmagento_interface', $this->emConf );

		// get Magento Customer
		$this->_mageCustomer = Mage::getSingleton('customer/customer')->setWebsiteId($this->emConf['website']);
		$this->getMageCustomer()->loadByEmail($this->login['uname']);
		$this->getMageCustomer()->getAttributes();

		switch (true) {
			// Magento Customer and TYPO3 Frontend User already exist
			case $this->getMageCustomer()->getData('typo3_uid')
				&& $this->_loadUserByFieldValue('uid', $this->getMageCustomer()->getData('typo3_uid')):

				$uid = $this->_createOrUpdateFrontendUser($this->getMageCustomer()->getData('typo3_uid'));

				return $this->_loadUserByFieldValue('uid', $uid);
				break;

			// Magento Customer exists but TYPO Frontend User does not
			case $this->getMageCustomer()->getId()
				&& $this->_loadUserByFieldValue('username', $this->login['uname']) === null:

				$uid = $this->_createOrUpdateFrontendUser();

				$this->getMageCustomer()->setData('typo3_uid', $uid);
				$this->getMageCustomer()->getResource()->saveAttribute($this->getMageCustomer(), 'typo3_uid');

				return $this->_loadUserByFieldValue('uid',  $feUsers->getData ( 'uid' ) );
				break;

			// Magento Customer and TYPO3 User exist but with no link
			case $this->getMageCustomer()->getId()
				&& ($feUser = $this->_loadUserByFieldValue('username', $this->login['uname'])):

				$uid = $this->_createOrUpdateFrontendUser($feUser['uid']);

				$this->getMageCustomer()->setData( 'typo3_uid', $uid );
				$this->getMageCustomer()->getResource()->saveAttribute( $this->getMageCustomer(), 'typo3_uid' );

				return $this->_loadUserByFieldValue('uid',  $uid );
				break;

			// Magento Customer does not exist but TYPO3 Frontend User
			case !$this->getMageCustomer()->getId() && $this->_loadUserByFieldValue('username', $this->login['uname']):

				$feUser = $this->_loadUserByFieldValue('username', $this->login['uname']);

				$fields = array(
					'email' => $feUser['username'],
					'lastname' => $feUser['name'],
					'firstname' => $feUser['firstname'],
					'password'	=> $feUser['password'],
					'typo3_uid' => $feUser['uid'],
					'group_id' => $this->getMageCustomer()->getGroupId()
				);

				$this->getMageCustomer()->setData($fields)->save();
				$feUser['tx_fbmagento_id'] = $this->getMageCustomer()->getId();
				$this->_createOrUpdateFrontendUser($feUser['uid']);

				return $feUser;
				break;
		}
		return false;
	}

	/**
	 * create or update a TYPO3 fe_user
	 *
	 * @param unknown_type $id
	 */
	protected function _createOrUpdateFrontendUser($id=null) {

		$feUsers = Mage::getSingleton ('Flagbit_Typo3connect/Typo3_FeUsers');
		if ($id != null) {
			$feUsers->load($id);
		}

		$fields = array (
			'username' => $this->getMageCustomer()->getData('email'),
			'name' => $this->getMageCustomer()->getData('lastname'),
			'firstname' => $this->getMageCustomer()->getData('firstname'),
			'email' => $this->getMageCustomer()->getData('email'),
			'password' => $this->getMageCustomer()->getData('password_hash'),
			'pid' => Mage::helper('Flagbit_Typo3connect')->getConfigData('fe_user_pid'),
			'usergroup' => $this->getMageCustomer()->getData('typo3_group_id') ? $this->getMageCustomer()->getData('typo3_group_id') : Mage::helper('Flagbit_Typo3connect')->getConfigData('fe_user_group_uid'),
			'tx_fbmagento_id' => $this->getMageCustomer()->getId()
		);
		$feUsers->addData($fields);
		$feUsers->save();

		return $feUsers->getId();
	}

	/**
	 * get Mage Customer
	 *
	 * @return Mage_Customer_Model_Customer
	 */
	protected function getMageCustomer() {

		return $this->_mageCustomer;
	}

	/**
	 * load a TYPO3 fe_user by field and value
	 *
	 * @param unknown_type $field
	 * @param unknown_type $value
	 */
	protected function _loadUserByFieldValue($field, $value) {

		if (empty($_feUserCache[$field][$value])) {

			$_feUserCache[$field][$value] = null;

			$dbres = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
						'*',
						$this->db_user['table'],
						'pid='.intval(Mage::helper ( 'Flagbit_Typo3connect' )->getConfigData ( 'fe_user_pid' )).
							($this->db_user['checkPidList'] ? ' AND pid IN ('.$GLOBALS['TYPO3_DB']->cleanIntList($this->db_user['checkPidList']).')' : '').
							' AND '.$field.' = "'.$GLOBALS['TYPO3_DB']->quoteStr($value, $this->db_user['table']).'"'.
							$this->db_user['enable_clause']
			);

			if ($dbres && $GLOBALS['TYPO3_DB']->sql_num_rows($dbres)) {
				$_feUserCache[$field][$value] = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($dbres);
				$GLOBALS['TYPO3_DB']->sql_free_result($dbres);
			}
		}

		return $_feUserCache[$field][$value];
	}

	/**
	 * Authenticate a user
	 *
	 * @param array Data of user.
	 * @return boolean
	 */
	function authUser($user) {
		// get Extension Config
		$this->emConf = tx_fbmagento_tools::getExtConfig();

		// get an Magento Instance
		$this->mage = t3lib_div::makeInstance('tx_fbmagento_interface', $this->emConf );

		if (empty($user['tx_fbmagento_id'])){
			return 100;
		}

		$customer = Mage::getSingleton('customer/customer')->setWebsiteId($this->emConf['website'])->load($user['tx_fbmagento_id']);

		if ($customer->getConfirmation() && $customer->isConfirmationRequired()
			|| !$customer->validatePassword($this->login['uident'])
			|| $customer->getId() != $user['tx_fbmagento_id']) {
			return 100;
		}

		try {
			Mage::getSingleton('customer/session')->login($this->login['uname'], $this->login['uident']);
		} catch(Exeption $e) {
			return 100;
		}

		return 200;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fb_magento/lib/class.tx_fbmagento_auth_sv1.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fb_magento/lib/class.tx_fbmagento_auth_sv1.php']);
}

?>
