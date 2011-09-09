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
	public function __construct($url, $username, $password) {

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
	public function __call($name, $params) {

		if ($this->resource) {
			$resource = $this->resource;
			$this->resource = null;
			$result = $this->call($resource.'.'.$name, $params);

			return $result;
		} else {
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
	public function enableCache($type = 'memory') {

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
	public function call($resource, $params=array()) {

		if ($this->cache) {
			$cacheId = md5($resource.serialize($params));
			if ($this->getCache()->hasData($cacheId)) {
				return $this->getCache()->getData($cacheId);
			}
		}

		$result = $this->getClient()->call($this->sessionId, $resource, $params);
		
		if ($this->cache) {
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
		return t3lib_div::makeInstance('tx_fbmagento_cache', $this->cache);
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

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fb_magento/lib/class.tx_fbmagento_soapinterface.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fb_magento/lib/class.tx_fbmagento_soapinterface.php']);
}

?>
