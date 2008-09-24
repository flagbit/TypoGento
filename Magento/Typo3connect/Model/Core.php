<?php

class Flagbit_Typo3connect_Model_Core {



    protected  $_output = '';  
    protected $_blocks = array();  
    protected $_pi_base = null;  
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
    
    public function setPiBaseObj(&$obj){
    	$this->_pi_base = &$obj;
    	return $this;
    }
    
    
    public function getPiBaseObj(){
    	return $this->_pi_base;
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