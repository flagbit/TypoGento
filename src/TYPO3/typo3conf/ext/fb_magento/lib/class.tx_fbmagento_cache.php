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
 * TypoGento Cache
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class tx_fbmagento_cache implements t3lib_Singleton {
	
	/**
	 * Cache Handler
	 *
	 * @var ArrayObject
	 */
	protected $_handler = null;

	/**
	 * Setter/Getter underscore transformation cache
	 *
	 * @var array
	 */
	protected static $_underscoreCache = array();


	/**
	 * constructor function
	 *
	 * @return void
	 */
	public function __construct() {
		$this->initializeTypo3Cache();
	}

    /**
     * Initialize cache instance to be ready to use
     *
     * @return void
     */
    protected function initializeTypo3Cache() {
        \TYPO3\CMS\Core\Cache\Cache::initializeCachingFramework();
		$this->_handler = $GLOBALS['typo3CacheManager']->getCache('fb_magento_cache');
    }

	/**
	 * return Handler
	 *
	 * @return ArrayObject
	 */
	protected function getHandler(){
		return $this->_handler;
	}

	/**
	 * Set/Get attribute wrapper
	 *
	 * @param string $method
	 * @param array $args
	 * @return mixed
	 */
	public function __call($method, $args) {
		switch (substr($method, 0, 3)) {
			case 'get' :
				$key = $this->_underscore(substr($method,3));
				return $this->getData($key);

			case 'set' :
				$key = $this->_underscore(substr($method,3));
				return $this->setData($key, $args[0]);

			case 'uns' :
				$key = $this->_underscore(substr($method,3));
				return $this->unsData($key);

			case 'has' :
				$key = $this->_underscore(substr($method,3));
				return $this->hasData($key);
		}

		tx_fbmagento_tools::throwException("Invalid method " . get_class($this) . "::" . $method . "(" . print_r($args, 1) . ")");
	}

	/**
	 * get Data
	 *
	 * @param string $key
	 * @return void
	 */
	public function getData($key) {
		return $this->getHandler()->get($key);
	}

	/**
	 * set Data
	 *
	 * @param string $key
	 * @param mixed $value
	 * @param integer $expire, cache expire time in seconds, default 0 = unlimited
	 * @return boolean
	 */
	public function setData($key, $value, $expire = 0) {
		$value = isset($value) ? $value : null;
		return $this->getHandler()->set($key, $value, array('typogento'), $expire);
	}

	/**
	 * isset Data
	 *
	 * @param string $key
	 * @return boolean
	 */
	public function hasData($key) {
		return isset($this->getHandler()->get($key));
	}

	/**
	 * unset Data
	 *
	 * @param string $key
	 * @return boolean
	 */
	public function unsData($key) {
		return $this->getHandler()->remove($key);
	}

	/**
	 * Converts UpperCamelCase to upper_camel_case
	 * Used for field names for setters and geters
	 * Uses cache to eliminate unneccessary preg_replace
	 *
	 * @param string $name
	 * @return string
	 */
	protected function _underscore($name) {
		if (isset(self::$_underscoreCache[$name])) {
			return self::$_underscoreCache[$name];
		}

		$result = strtolower(preg_replace('/(.)([A-Z])/', "$1_$2", $name));
		self::$_underscoreCache[$name] = $result;
		return $result;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fb_magento/lib/class.tx_fbmagento_cache.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fb_magento/lib/class.tx_fbmagento_cache.php']);
}

?>