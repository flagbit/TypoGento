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
 * @version $Id$
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class Flagbit_Typo3connect_Controller_Response extends Mage_Core_Controller_Response_Http {
	
	/**
	 * Echo the body segments
	 *
	 * @return void
	 */
	public function appendBody($output, $name = null) {
		
		if (Mage::app ()->getFrontController ()->getRequest ()->isXmlHttpRequest ()) {
			echo $output;
			exit ();
		}
		
		parent::appendBody ( $output, $name );
		Mage::getSingleton ( 'Flagbit_Typo3connect/Core' )->addOutput ( $output );
	}
	
	/**
	 * Echo the body segments
	 *
	 * @return void
	 */
	public function outputBody() {
		
		if (Mage::app ()->getFrontController ()->getRequest ()->isXmlHttpRequest ()) {
			parent::outputBody ();
			exit ();
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
		
		if (Mage::app ()->getFrontController ()->getRequest ()->isXmlHttpRequest ()) {
			echo $content;
			exit ();
		}
		
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
		
		if (strstr ( $url, Mage::app ()->getStore ()->getBaseUrl () )) {
			$url = Mage::getSingleton ( 'Flagbit_Typo3connect/Core' )->getTypolink ();
		}
		
		$this->canSendHeaders ( true );
		$this->setHeader ( 'Location', t3lib_div::locationHeaderUrl ( $url ), true )->setHttpResponseCode ( $code );
		$this->sendHeaders ();
		$this->_isRedirect = true;
		
		header ( 'Location: ' . t3lib_div::locationHeaderUrl ( $url ) );
		exit ();
		
		return $this;
	}

}