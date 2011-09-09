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
		// if module is not active -> use standard Router
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
			$urlModel = Mage::getModel('core/url')->setStore(Mage::app()->getStore());
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
		
		//checkings before even try to findout that current module
        //should use this router
        if (!$this->_beforeModuleMatch()) {
            return false;
        }

        $this->fetchDefault();

        $front = $this->getFront();

        $p = explode('/', trim($request->getPathInfo(), '/'));

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
            if (Mage::app()->getStore()->isAdmin()) {
                $module = 'admin';
            } else {
                return false;
            }
        }

        /**
         * Searching router args by module name from route using it as key
         */
        $modules = $this->getModuleByFrontName($module);

        /**
         * If we did not found anything  we searching exact this module
         * name in array values
         */
        if ($modules === false) {
            if ($moduleFrontName = $this->getModuleByName($module, $this->_modules)) {
                $modules = array($module);
                $module = $moduleFrontName;
            } else {
                return false;
            }
        }

        //checkings after we foundout that this router should be used for current module
        if (!$this->_afterModuleMatch()) {
            return false;
        }

        /**
         * Going through modules to find appropriate controller
         */
        $found = false;
        foreach ($modules as $realModule) {
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

            // get action name
            if (empty($action)) {
                if ($request->getActionName()) {
                    $action = $request->getActionName();
                } else {
                    $action = !empty($p[2]) ? $p[2] : $front->getDefault('action');
                }
            }

            //checking if this place should be secure
            $this->_checkShouldBeSecure($request, '/'.$module.'/'.$controller.'/'.$action);       	
            $controllerClassName = $this->_validateControllerClassName($realModule, $controller);

            if (!$controllerClassName) {
                continue;
            }

            // instantiate own controller class
			$controllerInstance = new $controllerClassName($request, Mage::getSingleton('Flagbit_Typo3connect/Core')->getResponse());
            
			if (!$controllerInstance->hasAction($action)) {
                continue;
            }

            $found = true;
            break;
        }

        /**
         * if we did not found any siutibul
         */
        if (!$found) {
            if ($this->_noRouteShouldBeApplied()) {
                $controller = 'index';
                $action = 'noroute';

                $controllerClassName = $this->_validateControllerClassName($realModule, $controller);
                if (!$controllerClassName) {
                    return false;
                }

                // instantiate own controller class
                $controllerInstance = new $controllerClassName($request, Mage::getSingleton('Flagbit_Typo3connect/Core')->getResponse());

                if (!$controllerInstance->hasAction($action)) {
                    return false;
                }
            } else {
                return false;
            }
        }

        // set values only after all the checks are done
        $request->setModuleName($module);
        $request->setControllerName($controller);
        $request->setActionName($action);

        // set parameters from pathinfo
        for ($i=3, $l=sizeof($p); $i<$l; $i+=2) {
            $request->setParam($p[$i], isset($p[$i+1]) ? $p[$i+1] : '');
        }

        // dispatch action
        $request->setDispatched(true);
        $controllerInstance->dispatch($action);

        return true;
	}

}
