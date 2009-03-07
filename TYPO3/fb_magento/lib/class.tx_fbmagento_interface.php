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
 * TypoGento Interface
 *
 * @version $Id$
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class tx_fbmagento_interface {

	/**
	 * Singleton instance
	 *
	 * @var tx_fbmagento_interface
	 */
	protected static $_instance = null;	
	
	
	/**
	 * allready dispatched?
	 *
	 * @var boolan
	 */
	protected $allreadyDispatched=false;
	
	/**
	 * instance of Flagbit_Typo3connect
	 *
	 * @var Flagbit_Typo3connect
	 */
	public $connector = null;
	
	/**
	 * enable or disable debug Mode
	 */
	public $debug = true;	
	
	/**
	 * Singleton pattern implementation makes "new" unavailable
	 *
	 * @return void
	 */
	private function __construct() {
	}
	
	/**
	 * Singleton pattern implementation makes "clone" unavailable
	 *
	 * @return void
	 */
	private function __clone() {
	}
	
	/**
	 * Returns an instance of tx_fbmagento_interface
	 *
	 * Singleton pattern implementation
	 *
	 * @param array emConf
	 * @return tx_fbmagento_interface Provides a fluent interface
	 */
	public static function getInstance(array $config) {
		if (null === self::$_instance) {
			self::$_instance = new self ( );
			self::$_instance->init($config);
		}
		
		return self::$_instance;
	}

	/**
	 * init Interface
	 *
	 * @param array emConf
	 */
	public function init($config){
		
		// init Config Array
		$this->config = $config;
		
		// include Mage
		if(!class_exists('Mage', false)){
			require_once ($this->config ['path'] . 'app/Mage.php');
		}
		
		// disable Notices
		error_reporting ( E_ALL & ~ E_NOTICE );		
		
		// overwrite Magento Autoload Funktion
		spl_autoload_register ( array (&$this, 'autoload' ) );	

		// Init Mage
		$store = tx_fbmagento_tools::getFELangStoreCode();
		Mage::app()->setCurrentStore(Mage::app()->getStore($store));

		if($GLOBALS['TSFE']->cObj instanceof tslib_cObj) {
			$cObj = $GLOBALS['TSFE']->cObj;
			$baseUrl = $cObj->getTypoLink_URL($GLOBALS['TSFE']->id);
		}
		
		// Init Typo3connect
		$params = array ('enabled' => true);
		if ('' != $baseUrl) {
			$params['_typo3BaseUrl'] = t3lib_div :: locationHeaderUrl($baseUrl);
		}
		$this->connector = Mage::getSingleton ( 'Flagbit_Typo3connect/Core',  $params);
		
		if (null !== $cObj) {
			$this->connector->setcObj ( $cObj );
		}
				
	}
	
	/**
	 * start Mage dispatch process with injected params
	 *
	 * @param array $params
	 * @return boolan
	 */
	public function dispatch($params){
		
		if(!$this->allreadyDispatched){
			$this->connector->dispatch($params);
		}
		
		$this->allreadyDispatched = true;
		
		return true;
	}
	
	/**
	 * get an Magento Content Block by Name
	 *
	 * @param string $identifier
	 * @return string HTML Code
	 */
	public function getBlock($identifier){
		
		$block = $this->connector->getBlock ( $identifier);
		
		if($block instanceof Mage_Core_Block_Abstract){
			return $this->connector->getBlock ( $identifier);
		}else {
			return null;
		}
	}
	
	/**
	 * call Connector Functions directly
	 *
	 * @param string $name
	 * @param array $args
	 * @return unknown
	 */
	public function __call($name, $args) {
		return call_user_func_array(array($this->connector, $name), $args);
	}
	
	/**
	 * generate Headerdata from Shopsystem
	 *
	 * @return string
	 */
	public function getHeaderData(){
		 	
		$objHead = $this->getBlock( 'head' );
		$head = array();
		
		if($objHead instanceof Mage_Page_Block_Html_Head){

			$head[] = '<script type="text/javascript">';
			$head[] = '//<![CDATA[';
			$head[] = '    var BLANK_URL = \''.$objHead->helper('core/js')->getJsUrl('blank.html').'\'';
			$head[] = '    var BLANK_IMG = \''.$objHead->helper('core/js')->getJsUrl('spacer.gif').'\'';
			$head[] = '//]]>';
			$head[] = '</script>';
			$head[] = $objHead->getCssJsHtml();
			$head[] = $objHead->getChildHtml();
			$head[] = $objHead->helper('core/js')->getTranslatorScript();
			
		}
		
		return implode("\n", $head);
	}
	
	public function getBodyData(){
		Mage::log(get_class($this->connector->getResponse()));
		return $this->connector->getResponse()->outputBody(true);
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

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fb_magento/lib/class.tx_fbmagento_interface.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fb_magento/lib/class.tx_fbmagento_interface.php']);
}

?>