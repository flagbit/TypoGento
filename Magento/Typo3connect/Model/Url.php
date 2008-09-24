<?php

class Flagbit_Typo3connect_Model_Url extends Mage_Core_Model_Url {


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
	public function getUrl($routePath=null, $routeParams=null)
	{
		
		if(!Mage::getSingleton('Flagbit_Typo3connect/Core')->isEnabled()){
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



		/**
		 * Apply query params, need call after getRouteUrl for rewrite _current values
		 */
		if (isset($routeParams['_query'])) {
			if (is_string($routeParams['_query'])) {
				$this->setQuery($routeParams['_query']);
			} elseif (is_array($routeParams['_query'])) {
				$this->setQueryParams($routeParams['_query'], !empty($routeParams['_current']));
			}
			if ($routeParams['_query'] === false) {
				$this->setQueryParams(array());
			}
			unset($routeParams['_query']);
		}

		$session = Mage::getSingleton('core/session');
		if ($sessionId = $session->getSessionIdForHost($url)) {
			#$this->setQueryParam($session->getSessionIdQueryParam(), $sessionId);
		}

		if ($query = $this->getQuery($escapeQuery)) {
			#$url .= '?'.$query;
		}

		if ($this->getFragment()) {
			//$url .= '#'.$this->getFragment();
		}
		
		
		$params = array(
			"route" => $this->getRouteName(),
			"controller" => $this->getControllerName(),
			"action" => $this->getActionName(),
		);
		if($routeParams) $params = array_merge($params, $routeParams);

		return Mage::getSingleton('Flagbit_Typo3connect/Core')->getTypolink($params);
	}


	public function getRouteUrl($routePath=null, $routeParams=null)
	{
		
		if(!Mage::getSingleton('Flagbit_Typo3connect/Core')->isEnabled()){
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