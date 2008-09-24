<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2008 Joerg Weller <weller@flagbit.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

require_once (PATH_tslib . 'class.tslib_pibase.php');

/**
 * Plugin 'Magento' for the 'fb_magento' extension.
 *
 * @author	Joerg Weller <weller@flagbit.de>
 * @package	TYPO3
 * @subpackage	tx_fbmagento
 */
class tx_fbmagento_pi1 extends tslib_pibase {
	var $prefixId = 'tx_fbmagento_pi1'; // Same as class name
	var $scriptRelPath = 'pi1/class.tx_fbmagento_pi1.php'; // Path to this script relative to the extension dir.
	var $extKey = 'fb_magento'; // The extension key.
	var $pi_checkCHash = true;
	var $emConf = null;
	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	public function main($content, $conf) {
		$this->conf = $conf;
		$this->pi_setPiVarDefaults ();
		$this->pi_loadLL ();
		
		// overwrite Magento Autoload Funktion
		spl_autoload_register ( array (&$this, 'autoload' ) );
		
		// Flexform
		$this->pi_initPIflexForm ();
		$this->view = $this->pi_getFFvalue ( $this->cObj->data ["pi_flexform"], 'show', 'main' );
		$product_id = $this->pi_getFFvalue ( $this->cObj->data ["pi_flexform"], 'product_id', 'main' );
		
		#print_r($this->cObj->data["pi_flexform"]);
		

		$this->emConf = unserialize ( $GLOBALS ['TYPO3_CONF_VARS'] ['EXT'] ['extConf'] ['fb_magento'] );
		
		require_once ($this->emConf ['path'] . 'app/Mage.php');
		
		// disable Notices
		error_reporting ( E_ALL & ~ E_NOTICE );
		
		// Init Mage
		Mage::app ();
		
		// Init Typo3connect
		$connector = Mage::getSingleton ( 'Flagbit_Typo3connect/Core', array ('enabled' => true ) );
		$connector->setPiBaseObj ( $this );
		
		// route throw piVars or Flexform 
		if ($this->piVars ['shop'] ['route']) {
			$params = $this->piVars ['shop'];	
		} else {
			switch ($this->view) {
				case "SINGLEPRODUCT" :
					$params = array ('route' => 'catalog', 'controller' => 'product', 'action' => 'view', 'id' => $product_id );
					break;
			}
		}
		
		$connector->setParams ( $params );
		
		// get Front Controller
		$front = Mage::app ()->getFrontController ();
		
		// run Dispatch
		$front->dispatch ();
		
		// header 
		$GLOBALS ['TSFE']->additionalHeaderData [] = $connector->getBlock ( 'head' );
		
		// get Content
		$content = $connector->getBlock ( 'top.links' );
		$content .= $connector->getBlock ( 'content' );
		
		#var_dump($connector->getBlocks());
		

		return $this->pi_wrapInBaseClass ( $content );
	}
	
	/**
	 * Class autoload
	 *
	 * @todo change to spl_autoload_register
	 * @param string $class
	 */
	public function autoload($class) {
		
		if (strpos ( $class, '/' ) !== false) {
			return;
		}
		$classFile = uc_words ( $class, DS ) . '.php';
		
		try{
			include ($classFile);
		}catch (Exception $m){
			// no output since TYPO3 Classes will also be loaded throw autoload
		}
	}

}

if (defined ( 'TYPO3_MODE' ) && $TYPO3_CONF_VARS [TYPO3_MODE] ['XCLASS'] ['ext/fb_magento/pi1/class.tx_fbmagento_pi1.php']) {
	include_once ($TYPO3_CONF_VARS [TYPO3_MODE] ['XCLASS'] ['ext/fb_magento/pi1/class.tx_fbmagento_pi1.php']);
}

?>