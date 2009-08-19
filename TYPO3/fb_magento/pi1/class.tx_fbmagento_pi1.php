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
 * TypoGento pi1
 *
 * @version $Id$
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
require_once (PATH_tslib . 'class.tslib_pibase.php');
require_once(t3lib_extmgm::extPath('fb_magento').'lib/class.tx_fbmagento_tools.php');
require_once(t3lib_extmgm::extPath('fb_magento').'lib/class.tx_fbmagento_interface.php');

class tx_fbmagento_pi1 extends tslib_pibase {
	var $prefixId = 'tx_fbmagento'; // Same as class name
	var $scriptRelPath = 'pi1/class.tx_fbmagento_pi1.php'; // Path to this script relative to the extension dir.
	var $extKey = 'fb_magento'; // The extension key.
	var $emConf = null;
	var $pi_checkCHash = true;
	
	/**
	 * The main method of the PlugIn
	 *
	 * @param	string		$content: The PlugIn content
	 * @param	array		$conf: The PlugIn configuration
	 * @return	The content that is displayed on the website
	 */
	public function main($content, $conf) {
		$this->conf = $conf;
		$this->pi_setPiVarDefaults ();
		$this->pi_loadLL ();
		$this->pi_USER_INT_obj=1;

		// Flexform
		$this->pi_initPIflexForm ();

		// get Extension Config
		$this->emConf = tx_fbmagento_tools::getExtConfig();
				
		// route throw piVars
		if ($this->piVars ['shop'] ['route']) {
			$params = $this->piVars ['shop'];	

		// route throw Typoscript
		} elseif (isset($this->conf['params.']['route'])){
			$params = $this->conf['params.'];

		// route throw Flexform
		} else{
			$params = array();
			if(!$this->view){
				$this->getRoutingDataFromPage();			
			}

			switch ($this->view) {
				case "SINGLEPRODUCT" :
					$product_id = $this->pi_getFFvalue ( $this->cObj->data ["pi_flexform"], 'product_id', 'main' );
					$params = array ('route' => 'catalog', 'controller' => 'product', 'action' => 'view', 'id' => $product_id );
					break;
					
				case "PRODUCTLIST" :
					$category_id = $this->pi_getFFvalue ( $this->cObj->data ["pi_flexform"], 'category_id', 'main' );
					$params = array ('route' => 'catalog', 'controller' => 'category', 'action' => 'view', 'id' => $category_id );
					break;
					
				case "USER" :
					$route = $this->pi_getFFvalue ( $this->cObj->data ["pi_flexform"], 'route', 'main' );
					$controller = $this->pi_getFFvalue ( $this->cObj->data ["pi_flexform"], 'controller', 'main' );
					$action = $this->pi_getFFvalue ( $this->cObj->data ["pi_flexform"], 'action', 'main' );
					$params = array ('route' => $route, 'controller' => $controller, 'action' => $action);
					break;					
			}

			$params = t3lib_div::array_merge_recursive_overrule($params, (array) $this->piVars ['shop']);
			
		}

		// get an Magento Instance
		$this->mage = tx_fbmagento_interface::getInstance( $this->emConf );
		$this->mage->setTsConfig($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_fbmagento_pi1.']);
		$this->mage->dispatch($params);
		
		// set Page Title
		$objHead = $this->mage->getBlock( 'head' );
		if($objHead instanceof Mage_Page_Block_Html_Head){
			$GLOBALS['TSFE']->page['title'] = $objHead->getTitle();
		}
		
		// render Block specified by Typoscript
		if(isset($this->conf['block'])){
			
			if($this->conf['block'] == 'typo3header'){
				return $this->mage->getHeaderData();
				
			}
			elseif($this->conf['block'] == '__responseBody'){
				$content .= $this->mage->getBodyData();
			}
			elseif($this->mage->getBlock( $this->conf['block'] ) !== null){
				$content .= $this->mage->getBlock( $this->conf['block'] )->toHtml ();
			}
			
			
		// render default Blocks	
		}else{
		
			// header 
			if($this->emConf['auto_header'] && $this->mage->getBlock( 'head' ) !== null){
				$GLOBALS['TSFE']->additionalHeaderData [] = $this->mage->getHeaderData();
				$GLOBALS['TSFE']->page['title'] = $this->mage->getBlock( 'head' )->getTitle();
			}
			
			// get Content
			if($this->mage->getBlock( 'content' ) !== null){
				$content .= $this->mage->getBlock( 'content' )->toHtml ();
			}
		}

		return isset($this->conf['nowrap']) && $this->conf['nowrap'] ? $content : $this->pi_wrapInBaseClass ( $content );
	}
	
	/**
	 * gets routing Data from the current Page an the included Plugins
	 *
	 * @return boolan
	 */
	protected function getRoutingDataFromPage(){
		
		$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('pi_flexform', 'tt_content', 'pid=\''.$GLOBALS ['TSFE']->id.'\' AND list_type=\'fb_magento_pi1\' '.$this->cObj->enableFields('tt_content'), 'sorting');
		foreach ((array) $rows as $row){
			if(!$row['pi_flexform']) continue;
			$this->cObj->data['pi_flexform'] = t3lib_div::xml2array($row['pi_flexform']);
			$this->view = $this->pi_getFFvalue ( $this->cObj->data ["pi_flexform"], 'show', 'main' );
			if($this->view){
				return true;
			}
		}
		return false;		
	}
	

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fb_magento/pi1/class.tx_fbmagento_pi1.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fb_magento/pi1/class.tx_fbmagento_pi1.php']);
}


?>
