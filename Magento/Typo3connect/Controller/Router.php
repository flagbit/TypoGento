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
 * TypoGento Router
 *
 * @version $Id$
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */

class Flagbit_Typo3connect_Controller_Router extends Mage_Core_Controller_Varien_Router_Standard{

	/**
	 * determines the Controller / Action throw Request Object
	 *
	 * @param Zend_Controller_Request_Http $request
	 * @return boolan
	 */
	public function match(Zend_Controller_Request_Http $request)
	{

		if(!Mage::getSingleton('Flagbit_Typo3connect/Core')->isEnabled()){
			return parent::match($request);
		}

		// get Params from TYPO3
		$params = Mage::getSingleton('Flagbit_Typo3connect/Core')->getParams();
		if ($params['route']) {
			$config = $params['route'];
			if ($params['controller']) {
				$config .= '/'.$params['controller'];
				if ($params['action']) {
					$config .= '/'.$params['action'];
				}
			}
			unset ($params['route'], $params['controller'], $params['action']);
			
			$frontController = Mage::app ()->getFrontController ();
			$urlModel = Mage::getModel('core/url');
			$oldUrl = '/'.$urlModel->getUrl($config, $params, true);
			
			// Remove the query string from REQUEST_URI
            if ($pos = strpos($oldUrl, '?')) {
                $oldUrl = substr($oldUrl, 0, $pos);
            }
			
            $request->setPathInfo($oldUrl);
			$frontController->rewrite();
		}
		
		if (Mage::app()->getStore()->isAdmin()) {
			return false;
		}

		$this->fetchDefault();
		$front = $this->getFront();
		
		// extract params to original information
		$p = explode('/', trim($request->getPathInfo(), '/'));
		
		// set path back to original path information to face Magento URL restrictions
		$request->setPathInfo($request->getOriginalPathInfo());
		// get module name
        if ($request->getModuleName()) {
            $module = $request->getModuleName();
        } else {
            if(!empty($p[0])) {
            	$module = $p[0];
            } else {
            	$module = $this->getFront()->getDefault('module');
                $request->setAlias(Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,	'');
            }
        }
        
        if (!$module) {
            return false;
        }
        
        $realModule = $this->getModuleByFrontName($module);
        if (!$realModule) {
            if ($moduleFrontName = array_search($module, $this->_modules)) {
                $realModule = $module;
                $module = $moduleFrontName;
            } else {
                return false;
            }
        }

        $request->setRouteName($this->getRouteByFrontName($module));

        // get controller name
        if ($request->getControllerName()) {
            $controller = $request->getControllerName();
        } else {
            if (!empty($p[1])) {
                $controller = $p[1];
            } else {
            	$controller = $front->getDefault('controller');
            	$request->setAlias(
            	   Mage_Core_Model_Url_Rewrite::REWRITE_REQUEST_PATH_ALIAS,
            	   ltrim($request->getOriginalPathInfo(), '/')
            	);
            }
        }
        
        $controllerFileName = $this->getControllerFileName($realModule, $controller);
        if (!$this->validateControllerFileName($controllerFileName)) {
            return false;
        }

        $controllerClassName = $this->getControllerClassName($realModule, $controller);
        if (!$controllerClassName) {
            return false;
        }

        // get action name
        if (empty($action)) {
            if ($request->getActionName()) {
                $action = $request->getActionName();
            } else {
                $action = !empty($p[2]) ? $p[2] : $front->getDefault('action');
            }
        }
        $this->_checkShouldBeSecure($request, '/'.$module.'/'.$controller.'/'.$action);

        // include controller file if needed
        if (!class_exists($controllerClassName, false)) {
            if (!file_exists($controllerFileName)) {
                return false;
            }
            include $controllerFileName;
            if (!class_exists($controllerClassName, false)) {
                throw Mage::exception('Mage_Core', Mage::helper('core')->__('Controller file was loaded but class does not exist'));
            }
        }
		
		// instantiate controller class
		$controllerInstance = new $controllerClassName($request, Mage::getSingleton('Flagbit_Typo3connect/Core')->getResponse());
		if (!$controllerInstance->hasAction($action)) {
			return false;
		}
		
		$request->setModuleName($module);
		$request->setControllerName($controller);
		$request->setActionName($action);
		$request->setParams($params);

		// dispatch action
		$request->setDispatched(true);
		$controllerInstance->dispatch($action);

		return true;
	}
}
