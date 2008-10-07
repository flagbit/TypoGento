<?php

class Flagbit_Typo3connect_Model_Core {
	
	protected $_output = '';
	protected $_blocks = array ();
	protected $_cObj = null;
	protected $_params = array ();
	
    /**
     * Response object
     *
     * @var Zend_Controller_Response_Http
     */
    protected $_response;	
	
    /**
     * Response object
     *
     * @var Flagbit_Typo3connect_Model_Core
     */
    protected static $_instance;	
	
	/**
	 * Router
	 *
	 * @var Flagbit_Typo3connect_Controller_Router
	 */
	protected $_router = null;
	
	public function __construct($params = array()) {
		
		foreach ( $params as $key => $value ) {
			$this->$key = $value;
		}
		
		if ($this->isEnabled ()) {
			
			// init Typo3Connect Router and set it
			$this->_router = new Flagbit_Typo3connect_Controller_Router ( );
			$this->_router->collectRoutes ( 'frontend', 'standard' );
			Mage::app ()->getFrontController ()->addRouter ( 'standard', $this->_router );
		}
	
	}
	
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
            $this->_response->headersSentThrowsException = Mage::$headersSentThrowsException;
            $this->_response->setHeader("Content-Type", "text/html; charset=UTF-8");
        }
        return $this->_response;
    }	
	
	/**
	 * start Mage dispatch process with injected params
	 *
	 * @param array $params
	 * @return boolan
	 */
	public function dispatch($params){
		
		// set dispatch Params
		$this->setParams ( $params );
		
		try{
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
	
	public function getTypolinkKeepPIvars(array $overruleParams = array()) {
		
		$get = t3lib_div::_GET();
		$piVars = $get['tx_fbmagento']['shop'];
		$piVars = array_merge((array) $this->getParams(), (array) $piVars);
		unset($piVars['DATA']);
		$params = t3lib_div::array_merge_recursive_overrule((array) $piVars,$overruleParams);
		return $this->getcObj ()->getTypoLink_URL ( $GLOBALS ['TSFE']->id, array ('tx_fbmagento' => array ('shop' => $params ) ) );
	}
	
	public function getTypolink(array $params = array()) {
		return $this->getcObj ()->getTypoLink_URL ( $GLOBALS ['TSFE']->id, array ('tx_fbmagento' => array ('shop' => $params ) ) );
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
	
	public function setParams(array $params = array()) {
		$this->_params = $params;
		return $this;
	}
	
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
	
	public function setBlock($name, $html) {
		$this->_blocks [$name] = $html;
	}
	
	public function getBlock($name) {
		return $this->_blocks [$name];
	}
	
	public function getBlocks() {
		return $this->_blocks;
	}
	
	public function addOutput($output) {
		
		$this->_output .= $output;
		return $this;
	}
	
	public function getOutput() {
		return $this->_output;
	}

}