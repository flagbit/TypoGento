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
 * TypoGento Interface
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class tx_fbmagento_interface implements t3lib_Singleton {

	/**
	 * allready dispatched?
	 *
	 * @var boolean
	 */
	protected $allreadyDispatched = false;

	/**
	 * instance of Flagbit_Typo3connect
	 *
	 * @var Flagbit_Typo3connect
	 */
	public $connector = null;

	/**
	 * enable or disable debug Mode
	 */
	public $debug = true;
	
	/**
	 * constructor for tx_fbmagento_interface
	 * 
	 * @param array emConf
	 */
	public function __construct($config) {
		// init Config Array
		$this->config = $config;

		// include Mage
		if (!class_exists('Mage', false)) {
			require_once($this->config ['path'] . 'app/Mage.php');
		}

		// disable Notices
		error_reporting ( E_ALL & ~ E_NOTICE );

		// Init Mage
		$store = tx_fbmagento_tools::getFELangStoreCode();
		Mage::app()->setCurrentStore(Mage::app()->getStore($store));
		restore_error_handler();

		if ($GLOBALS['TSFE']->cObj instanceof tslib_cObj) {
			$cObj = $GLOBALS['TSFE']->cObj;
			$baseUrl = $cObj->getTypoLink_URL($GLOBALS['TSFE']->id);

			// get rid of trailing html because of Real URL
			$pos = strrpos($baseUrl, '.');
			$extension = substr($baseUrl, $pos);

			if (strpos($extension, '/') === false) {
				$baseUrl = substr($baseUrl, 0, $pos);
			}
		}

		// Init Typo3connect
		$params = array('enabled' => true);
		if ('' != $baseUrl) {
			$params['_typo3BaseUrl'] = t3lib_div::locationHeaderUrl($baseUrl);
		}
		$this->connector = Mage::getSingleton('Flagbit_Typo3connect/Core', $params);

		if (null !== $cObj) {
			$this->connector->setcObj($cObj);
		}
	}

	/**
	 * start Mage dispatch process with injected params
	 *
	 * @param array $params
	 * @return boolan
	 */
	public function dispatch($params) {

		if (!$this->allreadyDispatched) {
			$this->connector->dispatch($params);
		}

		$this->allreadyDispatched = true;

		return true;
	}

	/**
	 * get an Magento Content Block by Name
	 *
	 * @param string $identifier
	 * @return string HTML Code
	 */
	public function getBlock($identifier) {

		$block = $this->connector->getBlock($identifier);

		if ($block instanceof Mage_Core_Block_Abstract) {
			return $this->connector->getBlock($identifier);
		} else {
			return null;
		}
	}

	/**
	 * call Connector Functions directly
	 *
	 * @param string $name
	 * @param array $args
	 * @return unknown
	 */
	public function __call($name, $args) {
		return call_user_func_array(array($this->connector, $name), $args);
	}

	/**
	 * generate Headerdata from Shopsystem
	 *
	 * @return string
	 */
	public function getHeaderData() {

		$objHead = $this->getBlock('head');
		$head = array();

		if ($objHead instanceof Mage_Page_Block_Html_Head) {

			$head[] = '<script type="text/javascript">';
			$head[] = '//<![CDATA[';
			$head[] = 'var BLANK_URL = \'' . $objHead->helper('core/js')->getJsUrl('blank.html') . '\'';
			$head[] = 'var BLANK_IMG = \'' . $objHead->helper('core/js')->getJsUrl('spacer.gif') . '\'';
			$head[] = '//]]>';
			$head[] = '</script>';
			$head[] = $objHead->getCssJsHtml();
			$head[] = $objHead->getChildHtml();
			$head[] = $objHead->helper('core/js')->getTranslatorScript();

		}

		return implode("\n", $head);
	}

	public function getBodyData(){
		return $this->connector->getResponse()->outputBody(true);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fb_magento/lib/class.tx_fbmagento_interface.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fb_magento/lib/class.tx_fbmagento_interface.php']);
}

?>