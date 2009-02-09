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

require_once(t3lib_extmgm::extPath('fb_magento').'lib/class.tx_fbmagento_cache.php');

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
	private $resource = null;
	private $cache = false;
	
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
	 * enable Cache
	 *
	 * @param string $type
	 * @return $this
	 */
	public function enableCache($type = 'memory'){
		
		$this->cache = $type;
		return $this;
	}
	
	/**
	 * call Soap Interface
	 *
	 * @param string $resource
	 * @param array $params
	 * @return unknown
	 */
	public function call($resource, $params=array()){
		
		if($this->cache){
			$cacheId = md5($resource.serialize($params));
			if($this->getCache()->hasData($cacheId)){
				return $this->getCache()->getData($cacheId);
			}
		}
		
		$result = $this->getClient()->call($this->sessionId, $resource, $params);
		
		if($this->cache){
			$this->getCache()->setData($cacheId, $result);
		}
		
		return $result;
	}
	
	/**
	 * get Cachehandler
	 *
	 * @return tx_fbmagento_cache
	 */
	protected function getCache(){
		return tx_fbmagento_cache::getInstance($this->cache);
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