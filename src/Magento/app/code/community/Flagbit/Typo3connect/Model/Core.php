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
 * TypoGento Core Model
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class Flagbit_Typo3connect_Model_Core {
	
	protected $_output = '';
	protected $_blocks = array ();
	protected $_cObj = null;
	protected $_params = array ();
	protected $_TsConfig = array ();
	protected $_logoutAlreadyRuns = false;
	
	/**
	 * set for Debug Output
	 *
	 * @var unknown_type
	 */
	public $debug = false;
	
    /**
     * Response object
     *
     * @var Zend_Controller_Response_Http
     */
    protected $_response;
	
	/**
	 * Router
	 *
	 * @var Flagbit_Typo3connect_Controller_Router
	 */
	protected $_router = null;
	
	/**
	 * TYPO3 Base URL
	 *
	 * @var string $_typo3BaseUrl
	 */
	protected $_typo3BaseUrl = null;
	
	/**
	 * Class Constuctor
	 *
	 * @param array $params
	 */
	public function __construct($params = array()) {
		
		foreach ( $params as $key => $value ) {
			$this->$key = $value;
		}
		
		if ($this->isEnabled ()) {
			// init Typo3Connect Router and set it
			$this->_router = new Flagbit_Typo3connect_Controller_Router ( );
			$this->_router->collectRoutes ( 'frontend', 'standard' );
			Mage::app ()->getFrontController ()->addRouter ( 'standard', $this->_router );
			
			if ($this->getTypo3BaseUrl ()) {
				Mage::app ()->getStore ()->setTypo3BaseUrl ($this->getTypo3BaseUrl ());
			}
		}
	}
	
	/**
	 * is Magento is runnig throw TYPO3 respectively is it enabled 
	 *
	 * @return boolan
	 */
	public function isEnabled() {
		return (defined ( 'TYPO3_MODE' ) && TYPO3_MODE === 'FE' ? true : false);
	}
	
	
    /**
     * Retrieve response object
     *
     * @return Flagbit_Typo3connect_Controller_Response
     */
    public function getResponse(){
        if (empty($this->_response)) {
            $this->_response = new Flagbit_Typo3connect_Controller_Response();
        }
        return $this->_response;
    }
    
    public function getTypo3BaseUrl() {
    	return $this->_typo3BaseUrl;
    }
	
	/**
	 * start Mage dispatch process with injected params
	 *
	 * @param array $params
	 * @return boolean
	 */
	public function dispatch($params){
		
		try{
			// set dispatch Params
			$this->setParams ( $params );
			
			// get Front Controller
			$front = Mage::app ()->getFrontController ();
			
			// run Dispatch
			$front->dispatch ();
			
			// send Response
			$this->getResponse()->sendResponse();
			
		}catch (Exception $e){
			if($this->debug){
				if(strstr($e->getMessage(), 'reached 100')){
					var_dump($params);
				}
				throw $e;
			}
			return false;
		}

		return true;
	}   
    
    
	/**
	 * set TYPO3 cObj
	 *
	 * @param tslib_cObj $obj
	 * @return this
	 */
	public function setcObj(tslib_cObj &$obj) {
		$this->_cObj = &$obj;
		return $this;
	}
	
	/**
	 * generates an TYPO3 Link which keeps the current pi_Vars
	 *
	 * @param array $overruleParams
	 * @return string URL
	 */
	public function getTypolinkKeepPIvars(array $overruleParams = array()) {
		
		$get = t3lib_div::_GET();
		$piVars = $get['tx_fbmagento']['shop'];
		$piVars = array_merge((array) $this->getParams(), (array) $piVars);
		unset($piVars['DATA']);
		$params = t3lib_div::array_merge_recursive_overrule((array) $piVars,$overruleParams);
		return $this->getTypolink ( $params );
	}
	
	/**
	 * generates an TYPO3 Link
	 *
	 * @param array $params
	 * @return string URL
	 */
	public function getTypolink(array $params = array()) {
		return $this->getcObj ()->getTypoLink_URL ( $this->getTypo3SiteId($params), array ('tx_fbmagento' => array ('shop' => $params ) ) );
	}
	
	/**
	 * get TYPO3 Site ID
	 * set by Typoscript: plugin.tx_fbmagento_pi1.target_pids
	 *
	 * @param array $params
	 * @return int
	 */
	protected function getTypo3SiteId(array $params = array()){
		
		$compareParams = array('route', 'controller', 'action');
		$pid = $GLOBALS ['TSFE']->id;

		if(isset($this->_TsConfig['target_pids.']) && count($params)){
			
			foreach($this->_TsConfig['target_pids.'] as $routeSet){
				
				$setPid = true;
				
				foreach ($compareParams as $paramName){
					
					// default controller / action name
					if(!isset($params[$paramName]) or !$params[$paramName]){
						$params[$paramName] = 'index';
					}

					if((isset($params[$paramName]) && isset($routeSet[$paramName]) && $params[$paramName] != $routeSet[$paramName])
						|| (!isset($params[$paramName]) && isset($routeSet[$paramName]))
						){
							$setPid = false;
							break 1;
						}
					
				}

				if($setPid === true){
					$pid = $routeSet['pid'];
				}
			}
		}
		
		return $pid;
	}
	
	/**
	 * set Typoscript Config Array
	 *
	 * @param unknown_type $config
	 */
	public function setTsConfig($config) {
		$this->_TsConfig = (array) $config;
	}
	
	/**
	 * return TYPO3 cObj Reference
	 *
	 * @return tslib_cObj
	 */
	public function getcObj() {
		if ($this->_cObj instanceof tslib_cObj) {
			return $this->_cObj;
		} else {
			throw new Exception ( 'There is no TYPO3 cObj but it is needed' );
		}
	}
	
	/**
	 * set routing params
	 *
	 * @param array $params
	 * @return object $this
	 */
	public function setParams(array $params = array()) {
		$this->_params = $params;
		return $this;
	}
	
	/**
	 * get routing param
	 *
	 * @return array
	 */
	public function getParams() {
		return $this->_params;
	}
	
	/**
	 * Returns an instance of Flagbit_Typo3_Core
	 *
	 * Singleton pattern implementation
	 *
	 * @return Flagbit_Typo3_Core
	 */
	public static function getInstance() {
		if (null === self::$_instance) {
			self::$_instance = new self ( );
		}
		
		return self::$_instance;
	}
	
	/**
	 * logout both Magento Customer and TYPO3 Frontend User
	 * prevent infinite loops 
	 */
	public function logout(){
		
		if($this->_logoutAlreadyRuns === true){
			return;
		}
		
		$this->_logoutAlreadyRuns = true;
		
		// logout Magento 
		/*@var $customerSession Mage_Customer_Model_Session */
		$customerSession = Mage::getModel('customer/session');	
		$customerSession->logout();	

		// logout TYPO3
        $GLOBALS['TSFE']->fe_user->logoff();
		
	}
	
	/**
	 * set an Layout Block
	 *
	 * @param string $name
	 * @param Mage_Core_Block_Abstract $html
	 */
	public function setBlock($name, $block) {
		$this->_blocks [$name] = $block;
	}
	
	/**
	 * get an Layout Block
	 *
	 * @param string $name
	 * @return Mage_Core_Block_Abstract
	 */
	public function getBlock($name) {
		return $this->_blocks [$name];
	}
	
	/**
	 * return all Layout Blocks as Array
	 *
	 * @return array of Mage_Core_Block_Abstract
	 */
	public function getBlocks() {
		return $this->_blocks;
	}
	
	/**
	 * add something to output
	 *
	 * @param string $output
	 * @return object $this
	 */
	public function addOutput($output) {
		
		$this->_output .= $output;
		return $this;
	}
	
	/**
	 * returns the output
	 *
	 * @return string
	 */
	public function getOutput() {
		return $this->_output;
	}

}