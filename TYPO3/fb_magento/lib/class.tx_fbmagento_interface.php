<?php

class tx_fbmagento_interface {

	/**
	 * Singleton instance
	 *
	 * @var tx_fbmagento_interface
	 */
	protected static $_instance = null;	
	
	/**
	 * instance of Flagbit_Typo3connect
	 *
	 * @var Flagbit_Typo3connect
	 */
	protected $connector = null;
	
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
		Mage::app ();

		// Init Typo3connect
		$this->connector = Mage::getSingleton ( 'Flagbit_Typo3connect/Core', array ('enabled' => true ) );
		$this->connector->setcObj ( $GLOBALS['TSFE']->cObj );		
				
	}
	
	/**
	 * start Mage dispatch process with injected params
	 *
	 * @param array $params
	 * @return boolan
	 */
	public function dispatch($params){
		
		// set dispatch Params
		$this->connector->setParams ( $params );
		
		try{
			// get Front Controller
			$front = Mage::app ()->getFrontController ();
			
			// run Dispatch
			$front->dispatch ();
			
		}catch (Exception $e){
			
			if($this->debug){
				throw $e;
			}
			return false;
		}

		return true;
	}
	
	/**
	 * get an Magento Content Block by Name
	 *
	 * @param string $identifier
	 * @return string HTML Code
	 */
	public function getContent($identifier){
		return $this->connector->getBlock ( $identifier);
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

?>