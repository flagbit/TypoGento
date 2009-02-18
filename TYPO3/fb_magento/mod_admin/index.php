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
 * TYPO3 Backend Module tx_fbmagento_modadmin
 *
 * @version $Id: class.tx_fbmagento_pi1.php 19 2008-11-25 17:50:44Z weller $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
unset($MCONF);
require ('conf.php');
require ($BACK_PATH.'init.php');
require ($BACK_PATH.'template.php');

$BE_USER->modAccess($MCONF, 1);

require_once(t3lib_extmgm::extPath('fb_magento').'lib/class.tx_fbmagento_tools.php');
require_once(t3lib_extmgm::extPath('fb_magento').'lib/class.tx_fbmagento_interface.php');

class tx_fbmagento_modadmin {

	/**
	 * Configuration for the module
	 * @var	Array
	 */
	var $MCONF = array();

	/**
	 * The backend document
	 * @var	Object
	 */
	var $doc;

	/**
	 * The main method of the Plugin
	 *
	 * @return	Mixed		Either returns an error or sends a redirect header
	 */
	function main() {

		// Declare globals
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		
		$config = tx_fbmagento_tools::getExtConfig();

		header('Location: '.$config['url'].'admin/');

	}
	
	/**
	 * for singlesignon not used yet
	 *
	 */
	function createUser(){
			
		// get Extension Config
		$this->emConf = tx_fbmagento_tools::getExtConfig();
				
		// get an Magento Instance
		$this->mage = tx_fbmagento_interface::getInstance( $this->emConf );

		if (Mage::getSingleton('admin/session')->isLoggedIn()) {
			die('drin');
		}
		
		$username = 'typo3_'.$BE_USER->user['username'];
		
		$model = Mage::getModel('admin/user');
		
		/* @var $user Mage_Admin_Model_User */
		$user = $model->loadByUsername($username);
		if($user instanceof Mage_Admin_Model_User && $user->getId()){
			$result = $user->login($user, $BE_USER->user['password']);
			$user->getResource()->recordLogin($user);
			header('Location: '.$this->emConf['url'].'admin/');
			exit();
		}
		
		$data = array(
			'username' => $username,
			'firstname' => '',
			'lastname' => $BE_USER->user['realName'],
			'email' => $BE_USER->user['email'],
			'password' => $BE_USER->user['password'],
			'password_confirmation' => $BE_USER->user['password'],
			'is_active' => '1',
		);
		
        $model = Mage::getModel('admin/user');
        $model->setData($data);
        try {
            $model->save();
            $uRoles = (int) $BE_USER->user['tx_fbmagento_group'];
            if ( $uRoles ) {
                $model->setRoleIds(array($uRoles))
                    ->setRoleUserId($model->getUserId())
                    ->saveRelations();
                    die('ok');
            }
            Mage::getSingleton('adminhtml/session')->setUserData(false);

            return;
            
        } catch (Exception $e) {
			echo $e->getMessage();
        }		
	}

	/**
	 * Prints the content of the module directly to the browser
	 *
	 * @return	void
	 */
	function printContent()	{
		echo $this->content;
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