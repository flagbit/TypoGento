<?php
/***************************************************************
*  Copyright notice
*
*  (c) 1999-2005 Kasper Skaarhoj (kasperYYYY@typo3.com)
*  (c) 2006-2008 Andreas Kundoch (typo3@mehrwert.de)
* All rights reserved
*
* This script is part of the TYPO3 project. The TYPO3 project is
* free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* The GNU General Public License can be found at
* http://www.gnu.org/copyleft/gpl.html.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU General Public License for more details.
*
* This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   57: class SC_mod_tools_phpadmin_index
 *   76:     function main()
 *  146:     function printContent()
 *
 * TOTAL FUNCTIONS: 2
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

// Regular initialization. Must check if the user has access to the module.
// This determines if it appears in the menu and if this script will redirect.



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
		print_r($user);
		
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

?>