<?php

/**
 * Magento API Soap Interface
 *
 */
class tx_fbmagento_soapinterface {

	private $connection = null;
	private $sessionId = null;
	private $urlPostfix = 'api/soap/?wsdl';
	
	public function __construct($url, $username, $password){
		
		$this->connection = new SoapClient($url.$this->urlPostfix);
		$this->sessionId = $this->getClient()->login('admin', 'lingo123');
		
		// $this->apiResources = $this->getClient()->resources($this->sessionId); 
		//var_dump($this->getClient()->__getFunctions()); die();
		
	}
	
	public function __call($name, $params){
		
		if($this->resource){
			$result = $this->getClient()->call($this->sessionId, $this->resource.'.'.$name, $params);
			$this->resource = null;
			return $result;
		}else{
			$this->resource = $name;
			return $this;
		}
	}
	
	
	/**
	 * Enter description here...
	 *
	 * @return SoapClient
	 */
	public function getClient(){
		return $this->connection;
	}
	
}

?>