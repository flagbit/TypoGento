<?php

/**
 * Magento API Soap Interface
 *
 */
class tx_fbmagento_soapinterface {

	private $connection = null;
	private $sessionId = null;
	private $urlPostfix = 'api/soap/?wsdl';
	
	/**
	 * Constructor which needs Soap Connection Details
	 *
	 * @param string $url
	 * @param string $username
	 * @param string $password
	 */
	public function __construct($url, $username, $password){
		
		$this->connection = new SoapClient($url.$this->urlPostfix);
		$this->sessionId = $this->getClient()->login('admin', 'lingo123');
	}
	
	/**
	 * Magic function which enables SOAP Calls like: resource()->action();
	 *
	 * @param string $name
	 * @param array $params
	 * @return unknown
	 */
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
	 * get SoapCleint
	 *
	 * @return SoapClient
	 */
	public function getClient(){
		return $this->connection;
	}
	
}

?>