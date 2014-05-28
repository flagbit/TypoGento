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
 * TypoGento Response Controller
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class Flagbit_Typo3connect_Controller_Response extends Mage_Core_Controller_Response_Http {
	
	public $lastUrl = null;
	
	/**
	 * Echo the body segments
	 *
	 * @return void
	 */
	public function appendBody($output, $name = null) {
		
		$this->ajaxHandler($output);
		
		parent::appendBody ( $output, $name );
		Mage::getSingleton ( 'Flagbit_Typo3connect/Core' )->addOutput ( $output );
	}
	
	/**
	 * handle Ajax Requests
	 *
	 * @param string $output
	 */
	protected function ajaxHandler($output) {
		
		if(!Mage::app ()->getFrontController ()->getRequest ()->isXmlHttpRequest ()) return;
		
		if($GLOBALS['TSFE']->renderCharset){
			header('Content-Type: text/html; charset='.$GLOBALS['TSFE']->renderCharset);
		} 
		
		echo $output;
		exit ();
		
	}
	
	/**
	 * Echo the body segments
	 *
	 * @param boolean $returnBody
	 * @return void
	 */
	public function outputBody($returnBody = false) {
		
		$content = implode('', (array) $this->_body);
		
		if (! $returnBody){
			
			$this->ajaxHandler($content);
			
		}else{
			
			return $content;
		}
	}
	
	/**
	 * send the Response and exit
	 */
	public function sendResponse() {
		
		parent::sendResponse ();
		
		if ($this->isRedirect ()) {
			exit ();
		}
	}
	
	/**
	 * set Body
	 *
	 * @param string $content
	 * @param string $name
	 * @return $this
	 */
	public function setBody($content, $name = null) {

			// handle Checkout redirects
		if(strstr($content, 'paypal_standard_checkout') 
			or strstr($content, 'clickandbuy_checkout')
			or strstr($content, 'payone_checkout')
			or strstr($content, 'moneybookers_checkout')
			){
				
			echo $content;
			exit();
		}
		
		
		// not longer necessary because of the rewriting of the app Model we can change die Response Object everywhere
		//$this->ajaxHandler($content);
		parent::setBody ( $content, $name );
		
		return $this;
	}
	
	/**
	 * Set redirect URL
	 *
	 * Sets Location header and response code. Forces replacement of any prior
	 * redirects.
	 *
	 * @param string $url
	 * @param int $code
	 * @return Zend_Controller_Response_Abstract
	 */
	public function setRedirect($url, $code = 302) {
		// set last URL for the _isUrlInternal workaround
		if($url == Mage::app()->getStore()->getBaseUrl() && $this->lastUrl){
			$url = $this->lastUrl;
		}
		$this->canSendHeaders ( true );
		#$this->setHeader ( 'Location', t3lib_div::locationHeaderUrl ( $url ), true )->setHttpResponseCode ( $code );
		$this->sendHeaders ();
		$this->_isRedirect = true;
		
		header ( 'Location: ' . t3lib_div::locationHeaderUrl ( $url ) );
		exit ();
		
		return $this;
	}

}