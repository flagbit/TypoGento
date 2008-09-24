<?php

class Flagbit_Typo3connect_Controller_Response extends Mage_Core_Controller_Response_Http {
	
	/**
	 * Echo the body segments
	 *
	 * @return void
	 */
	public function appendBody($output, $name = null) {
		Mage::getSingleton ( 'Flagbit_Typo3connect/Core' )->addOutput ( $output );
	}
	
	/**
	 * Echo the body segments
	 *
	 * @return void
	 */
	public function outputBody() {
	
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

		$this->canSendHeaders ( true );
		$this->setHeader ( 'Location', t3lib_div::locationHeaderUrl($url), true )->setHttpResponseCode ( $code );
		$this->sendHeaders();

		return $this;
	}

}