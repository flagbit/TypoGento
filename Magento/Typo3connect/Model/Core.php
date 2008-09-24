<?php

class Flagbit_Typo3connect_Model_Core {



    protected  $_output = '';  
    protected $_blocks = array();  
    protected $_cObj = null;  
    protected $_params = array();
    
    /**
     * Router
     *
     * @var Flagbit_Typo3connect_Controller_Router
     */
    protected $_router = null;

    public function __construct($params = array()){
    	
    	foreach($params as $key => $value){
    		$this->$key = $value;
    	}

	if($this->isEnabled()){
    	
		// init Typo3Connect Router and set it
		$this->_router = new Flagbit_Typo3connect_Controller_Router();
		$this->_router->collectRoutes('frontend', 'standard');
		Mage::app()->getFrontController()->addRouter('standard', $this->_router);    	
	}
    	
    }
    
    public function isEnabled(){
    	return (defined('TYPO3_MODE') && TYPO3_MODE === 'FE' ? true : false);
    }
    
    /**
     * set TYPO3 cObj
     *
     * @param tslib_cObj $obj
     * @return this
     */
    public function setcObj(tslib_cObj &$obj){
    	$this->_cObj = &$obj;
    	return $this;
    }
    
    
    public function getTypolink(array $params){
    	return $this->getcObj()->getTypoLink_URL($GLOBALS['TSFE']->id, array('tx_fbmagento' => array('shop' => $params)));
    }
    
    /**
     * return TYPO3 cObj Reference
     *
     * @return tslib_cObj
     */
    public function getcObj(){
    	if($this->_cObj instanceof tslib_cObj){
    		return $this->_cObj;
    	}else{
    		throw new Exception('There is no TYPO3 cObj but it is needed');
    	}
    }
    
    public function setParams(array $params = array()){
    	$this->_params = $params;
    	return $this;
    }
        
    
    public function getParams(){
    	return $this->_params;
    }
        

    

    /**
     * Returns an instance of Flagbit_Typo3_Core
     *
     * Singleton pattern implementation
     *
     * @return Flagbit_Typo3_Core
     */
    public static function getInstance()
    {
        if (null === self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function setBlock($name, $html){
    	$this->_blocks[$name] = $html;
    }
    
    public function getBlock($name){
    	return $this->_blocks[$name];
    }

    public function getBlocks(){
    	return $this->_blocks;
    }    
        
    
    public function addOutput($output){

    	$this->_output .= $output;
    	return $this;
    }
    
    public function getOutput(){
    	return $this->_output;
    }    
    
}