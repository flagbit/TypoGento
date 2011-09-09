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
 * TypoGento Url Model
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class Flagbit_Typo3connect_Model_Url extends Mage_Core_Model_Url {

	/**
	 * class Constructor
	 *
	 */
	public function _construct(){
		parent::_construct();
	}


	/**
	 * Build url by requested path and parameters
	 *
	 * @param   string $routePath
	 * @param   array $routeParams
	 * @return  string
	 */
	public function getUrl($routePath=null, $routeParams=null, $getOriginalData=false)
	{
		
		if(!Mage::getSingleton('Flagbit_Typo3connect/Core')->isEnabled() || $getOriginalData){
			return parent::getUrl($routePath, $routeParams);	
		}
		
		$escapeQuery = false;

		if (isset($routeParams['_fragment'])) {
			$this->setFragment($routeParams['_fragment']);
			unset($routeParams['_fragment']);
		}

		if (isset($routeParams['_escape'])) {
			$escapeQuery = $routeParams['_escape'];
			unset($routeParams['_escape']);
		}

		$url = $this->getRouteUrl($routePath, $routeParams);

		$params = array(
			"route" => $this->getRouteName(),
			"controller" => $this->getControllerName(),
			"action" => $this->getActionName(),
		);
		if($routeParams) $params = array_merge($params, $routeParams);
		if(is_array($routeParams['_query'])) $params = array_merge($params, $routeParams['_query']);
		
		unset($params['_query']);
		unset($params['_use_rewrite']);
		
		if($params['_current']){
			unset($params['_current']);
			$url = Mage::getSingleton('Flagbit_Typo3connect/Core')->getTypolinkKeepPIvars($params);
		}else{
			$url = Mage::getSingleton('Flagbit_Typo3connect/Core')->getTypolink($params);
		}
		
	    // Complete the typolink URL absolute using the base url
		if (strpos($url, 'http') !== 0) {
			$urlComponents = parse_url($this->getBaseUrl());
			$url = $urlComponents['scheme'] . '://' . rtrim($urlComponents['host'], '/') . '/' . ltrim($url, '/');
		}
		
		// save last URL in Response for the _isUrlInternal workaround
		Mage::getSingleton('Flagbit_Typo3connect/Core')->getResponse()->lastUrl= $url;
		
		return $url;		
	}

	/**
	 * return the Route URL
	 *
	 * @param string $routePath
	 * @param array $routeParams
	 * @return string
	 */
	public function getRouteUrl($routePath=null, $routeParams=null, $getOriginalData=false)
	{
		
		if(!Mage::getSingleton('Flagbit_Typo3connect/Core')->isEnabled() || $getOriginalData){
			return parent::getRouteUrl($routePath, $routeParams);	
		}
		
		$this->unsetData('route_params');

		if (!is_null($routePath)) {
			$this->setRoutePath($routePath);
		}
		if (is_array($routeParams)) {
			$this->setRouteParams($routeParams, false);
		}

		$url = $this->getRoutePath($routeParams);
		//$url = $this->getBaseUrl().$this->getRoutePath($routeParams);
		return $url;
	}

}