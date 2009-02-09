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
 * TypoGento Realurl
 *
 * @version $Id: class.tx_fbmagento_tcafields.php 26 2009-01-23 08:38:02Z weller $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
require_once(t3lib_extmgm::extPath('fb_magento').'lib/class.tx_fbmagento_soapinterface.php');
require_once(t3lib_extmgm::extPath('fb_magento').'lib/class.tx_fbmagento_tools.php');

class tx_fbmagento_realurl {
	
	/**
	 * @var tx_realurl
	 */
	protected $_realurlRef = null;
	
	/**
	 * direction
	 *
	 * @var string
	 */
	protected $_direction = null;
	
	/**
	 * rewrite Category
	 *
	 * @param array $params
	 * @param object tx_realurl $ref
	 * @return string
	 */
	public function idRewrite($params, $ref){
	
		$this->setRealurlRef($ref);

		if($this->isRouteControllerAction('catalog', 'category', 'view')){
			
			$cfg = array(
				'alias_field' => 'name',
				'table' => 'typo3connect_catalog_category.urlkeys',
				'type' => 'idList',
				'storeview' => 1,
				'cache' => 'memory'
			);
			
			return $this->rewriter($cfg, $params);
		}

		return $params ['value'];
	}	
	
	/**
	 * rewrite Category
	 *
	 * @param array $params
	 * @param object tx_realurl $ref
	 * @return string
	 */
	public function categoryRewrite($params, $ref){
		
		$cfg = array(
			'alias_field' => 'name',
			'table' => 'typo3connect_catalog_category.urlkeys',
			'type' => 'idList',
			'storeview' => 1,
			'cache' => 'memory'
		);		
		
		$this->setRealurlRef($ref);

		return $this->rewriter($cfg, $params);
	}	
	
	/**
	 * check Route, Controller and Action
	 *
	 * @param string $route
	 * @param string $controller
	 * @param string $action
	 * @return boolan
	 */
	protected function isRouteControllerAction($route, $controller = null, $action = null){
		
		switch($this->_direction){
		
			case 'encode':
			
				$params = (array) $this->getRealurlRef()->orig_paramKeyValues;
				
				if($params['tx_fbmagento[shop][route]'] != $route){
					return false;
				}
				
				if($controller !== null && $params['tx_fbmagento[shop][controller]'] != $controller){
					return false;
				}
				
				if($action !== null && $params['tx_fbmagento[shop][action]'] != $action){
					return false;
				}
						
				break;
				
				
			case 'decode':
			
				$params = $this->getRealurlRef()->speakingURIpath_procValue;
			
				preg_match('|/shop/([^/]*)/([^/]*)/([^/]*)|', $params, $matches);
			
				
				if($matches[1] != $route){
					return false;
				}
				
				if($controller !== null && $matches[2] != $controller){
					return false;
				}
				
				if($action !== null && $matches[3] != $action){
					return false;
				}			
				break;
		}
		
		return true;
	}
	
	/**
	 * primary Rewriter
	 *
	 * @param array $cfg
	 * @param array $params
	 * @param string $extValues
	 * @return string
	 */
	protected function rewriter($cfg, $params) {

		$cfg['id_field'] = 'id';

		if ($params ['decodeAlias']) {	
			$this->_direction = 'decode';
			return $this->alias2id ( $cfg, $params ['value'] );
		} else {
			$this->_direction = 'encode';
			return $this->id2alias ( $cfg, $params ['value'] );
		}
	}
	
	/**
	 * set Reference for tx_realurl
	 *
	 * @param object $ref
	 * @return object $this
	 */
	protected function setRealurlRef($ref){
		
		$this->_realurlRef = $ref;
		return $this;
		
	}
	
	/**
	 * get Reference from tx_realurl
	 *
	 * @return object tx_realurl
	 */
	protected function getRealurlRef(){
		return $this->_realurlRef;
	}	
	
	/**
	 * translates an Id to an Alias
	 *
	 * @param array $cfg
	 * @param int $value
	 * @return string alias
	 */
	protected function id2alias($cfg, $value) {
		
		if(!$value) return;
		
		// is allready an Alias Cached then return it
		$lookupResult = $this->getRealurlRef()->lookUp_idToUniqAlias($cfg, $value, 0);
		if($lookupResult){
			return $lookupResult;
		}

		// get Extension Config
		$conf = tx_fbmagento_tools::getExtConfig();
		
		// do Soap Call		
		try {
			
			$soapClient = new tx_fbmagento_soapinterface($conf['url'], $conf['username'], $conf['password']);
			$soapClient->enableCache( isset($cfg['cache']) ? $cfg['cache'] : null);
			
			switch($cfg['type']){
				
				case 'idList':
					$result = $soapClient->call($cfg['table']);
					$newValue = $result[$value];
					break;
				
				default:
					$result = $soapClient->call($cfg['table'], array($value));		
					$newValue = $result[$cfg['alias_field']];	
					break;				
				
			}
	
		}catch (Exception $e){
			
			if ($this->getRealurlRef()->enableDevLog) {
				t3lib_div::devLog('Typogento Soap Error: '.$e->getMessage(), 'realurl', -1);
			}
			return $value;
		}
		
		return $this->getRealurlRef()->lookUp_newAlias($cfg, $newValue, $value, 0);
	}
	
	/**
	 * translates an Alias to an Id
	 *
	 * @param array $cfg
	 * @param string $value
	 * @return int Id
	 */
	protected function alias2id($cfg, $value) {
		
		$result = $this->getRealurlRef()->lookUp_uniqAliasToId($cfg, $value);

		return $result;
	}
	
	
	/**
	 * Generates additional RealURL configuration and merges it with provided configuration
	 *
	 * @param array $paramsDefault configuration
	 * @param tx_realurl_autoconfgen $pObjParent object
	 * @return array Updated configuration
	 */
	public function addMagentoConfig($params, &$pObj) {

		return array_merge_recursive ( $params ['config'], array (
			'postVarSets' => array (
				'_DEFAULT' => array (
			      'shoparticle' => array (
			          array (
			            'GETvar' => 'tx_fbmagento[shop][s]',
			          ),                                
			        ),			
			      'shop' => array (
			          array (
			            'GETvar' => 'tx_fbmagento[shop][route]',
			          ),          
			          array (
			            'GETvar' => 'tx_fbmagento[shop][controller]',
			          ),
			          array (
			            'GETvar' => 'tx_fbmagento[shop][action]',
			          ),
			          array (
			            'GETvar' => 'tx_fbmagento[shop][id]',
			            'userFunc' => 'EXT:fbmagento/lib/class.tx_fbmagento_realurl.php:&tx_fbmagento_realurl->idRewrite'            
			          ),          
			          array (
			            'GETvar' => 'tx_fbmagento[shop][category]',
			            'userFunc' => 'EXT:fbmagento/lib/class.tx_fbmagento_realurl.php:&tx_fbmagento_realurl->categoryRewrite'
			          ),
			          array (
			            'GETvar' => 'tx_fbmagento[shop][product]',
			          ),                                                              
			        ),
			      'shoppage' => array (
			          array (
			            'GETvar' => 'tx_fbmagento[shop][p]',
			          ),          
			          array (
			            'GETvar' => 'tx_fbmagento[shop][order]',
			          ),         
			          array (
			            'GETvar' => 'tx_fbmagento[shop][dir]',
			          )                             
			    	)                              
				)
			)
		));
	}

}

?>