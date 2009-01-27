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
 * TypoGento soapinterface
 *
 * @version $Id$
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
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
		$this->sessionId = $this->getClient()->login($username, $password);
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
			$result = $this->call($this->resource.'.'.$name, $params);
			$this->resource = null;
			return $result;
		}else{
			$this->resource = $name;
			return $this;
		}
	}
	
	/**
	 * call Soap Interface
	 *
	 * @param string $resource
	 * @param array $params
	 * @return unknown
	 */
	public function call($resource, $params=array()){
		return $this->getClient()->call($this->sessionId, $resource, $params);
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