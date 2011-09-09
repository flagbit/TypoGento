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
 * TypoGento Api Modules Model
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class Flagbit_Typo3connect_Model_Modules_Api extends Mage_Api_Model_Resource_Abstract
{
    /**
     * Retrieve Modules list
     *
     * @return array
     */
    public function items()
    {

		/* @var $routes Mage_Core_Model_Config_Element */
		$routes = Mage::getConfig()->getNode('frontend/routers')->xpath('//frontName');
			
		$res = array();
        foreach ($routes as $item) {
        	$res[] = (string) $item;
        }
        return $res;        
    }
    
    /**
     * Retrieve Actions List 
     *
     * @param string $frontName
     * @param string $controllerName
     * @return array
     */
    public function actions($frontName, $controllerName){
    	
		$module = $this->getModuleByFrontName($frontName);
		if(!$module) return array();

		$controllerPath = Mage::getModuleDir('controllers', $module);    	
    	$controllerFile = $controllerPath.DS.ucfirst($controllerName).'Controller.php';
		if(!file_exists($controllerFile)) return array();
    	
		require_once($controllerFile);
		
    	$controllerClass = $module.'_'.ucfirst($controllerName).'Controller';
    	
    	$methods = (array) get_class_methods($controllerClass);
    	$actions = array();
    	foreach ($methods as $method){

    		if(substr($method, -6) != 'Action') continue;
    		$actions[] = strtolower(preg_replace('/([A-Z]+)/','_\\1',substr($method, 0, (strlen($method)-6)))); 
    	}
		return $actions;
    }
    
    /**
     * Retrieve Controllers List
     *
     * @param string $frontName
     * @return array
     */
    public function controllers($frontName)
    {
		if(!$frontName) return array();

		$module = $this->getModuleByFrontName($frontName);
		if(!$module) return array();

		$controllerPath = Mage::getModuleDir('controllers', $module);
    	
		$controllerDirectory = dir($controllerPath);
		$controllers = array();
		
		while (false !== ($entry = $controllerDirectory->read())) {
			if(!strstr($entry, 'Controller.php')) continue;
			$controllers[] = strtolower(str_replace('Controller.php', '', $entry));
		}
		$controllerDirectory->close();		
    	
		return $controllers;
    }
    
    /**
     * get Module by FrontName
     *
     * @param string $frontName
     * @return string
     */
    protected function getModuleByFrontName($frontName){
    	
    	/* @var $routes Mage_Core_Model_Config_Element */
		$routes = Mage::getConfig()->getNode('frontend/routers');
	
    	foreach($routes->children() as $route){

    		if((string) $route->args->frontName == $frontName){
    			return (string) $route->args->module;
    		}
    	}    	
    }
    
} 