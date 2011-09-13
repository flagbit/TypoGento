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

unset($MCONF);
require ('conf.php');
require ($BACK_PATH.'init.php');
require ($BACK_PATH.'template.php');

$BE_USER->modAccess($MCONF, 1);

require_once(t3lib_extmgm::extPath('fb_magento').'lib/class.tx_fbmagento_tools.php');
require_once(t3lib_extmgm::extPath('fb_magento').'lib/class.tx_fbmagento_interface.php');

/**
 * TYPO3 Backend Module tx_fbmagento_modadmin
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */

class tx_fbmagento_modadmin {

	/**
	 * Configuration for the module
	 * @var Array
	 */
	var $MCONF = array();

	/**
	 * The backend document
	 * @var Object
	 */
	var $doc;

	/**
	 * The main method of the Plugin
	 *
	 * @return Mixed Either returns an error or sends a redirect header
	 */
	function main() {

		// Declare globals
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		if (empty($BE_USER->user['tx_fbmagento_group'])) {
			$this->accessDenied();
		}

		// get Extension Config
		$this->emConf = tx_fbmagento_tools::getExtConfig();

		// get an Magento Instance
		$this->mage = t3lib_div::makeInstance('tx_fbmagento_interface', $this->emConf );

		/*@var $mageUser Mage_Admin_Model_User */
		$mageUser = Mage::getSingleton('admin/user');

		$mageUser->loadByUsername($BE_USER->user['username']);

		if ($mageUser->getId()) {
			if ($mageUser->getRole()->getId() != $BE_USER->user['tx_fbmagento_group']) {
				$this->accessDenied('different Roles are set!');
			}
		} else {

			$mageUser->setData(array(
				'username' => $BE_USER->user['username'],
				'password' => $BE_USER->user['password'],
				'firstname' => $BE_USER->user['realName'],
				'lastname' => $BE_USER->user['realName'],
				'email' => $BE_USER->user['email'],
				'is_active' => true
			));

			$mageUser->save();
			$mageUser->setRoleIds(array($BE_USER->user['tx_fbmagento_group']))->setRoleUserId($mageUser->getUserId())->saveRelations();
		}

		/*@var $mageSession Mage_Admin_Model_Session */
		$mageSession = Mage::getSingleton('admin/session');

		// login User
		$this->loginMageUser($BE_USER->user['username']);
	}

	/**
	 * login Magento Backenduser
	 *
	 * @param string $username
	 */
	protected function loginMageUser($username) {

		try {
			$session = Mage::getSingleton('admin/session');

			if ($session->isLoggedIn()) {
				$requestUri = Mage::getSingleton('adminhtml/url')->addSessionParam()->getUrl('adminhtml/dashboard/*', array('_current' => true));
				header('Location: ' . $requestUri);
				die();
			}

			/* @var $user Mage_Admin_Model_User */
			$user = Mage::getModel('admin/user');
			$user->loadByUsername($username);

			if ($user->getId()) {

				Mage::dispatchEvent('admin_user_authenticate_after', array(
					'username' => $user->getUsername(),
					'password' => $user->getPassword(),
					'user' => $user,
					'result' => true,
				));

				$user->getRole();
				$user->getResource()->recordLogin($user);

				if (Mage::getSingleton('adminhtml/url')->useSecretKey()) {
					Mage::getSingleton('adminhtml/url')->renewSecretUrls();
				}

				$session->setIsFirstVisit(true);
				$session->setUser($user);
				$session->setAcl(Mage::getResourceModel('admin/acl')->loadAcl());

				$requestUri = Mage::getSingleton('adminhtml/url')->addSessionParam()->getUrl('adminhtml/dashboard/*', array('_current' => true));

				header('Location: ' . $requestUri);
			} else {
				$this->accessDenied('Magento Backend Login failed!');
			}
		} catch (Mage_Core_Exception $e) {
			$this->accessDenied('Magento Backend Login failed!');
		}
	}

	/**
	 * access Denied
	 *
	 * @param string $msg
	 */
	public function accessDenied($msg = null){

		if($msg === null){
			$msg = 'Access denied!';
		}

		tx_fbmagento_tools::displayError($msg, true);
	}
}

// Make instance:
$SOBE = t3lib_div::makeInstance('tx_fbmagento_modadmin');
$SOBE->main();
$SOBE->printContent();

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fb_magento/mod_admin/index.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fb_magento/mod_admin/index.php']);
}

?>