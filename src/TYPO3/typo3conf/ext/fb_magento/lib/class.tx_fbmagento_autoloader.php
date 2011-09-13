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
 * TypoGento Autoloader
 *
 * @author Frederic Gaus <gaus@flagbit.de>
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class tx_fbmagento_autoloader implements t3lib_Singleton {

	/**
	 * constructor for tx_fbmagento_interface
	 *
	 * @param array emConf
	 */
	public function __construct($config) {

		// init Config Array
		$this->config = $config;

		// include Mage
		if(!class_exists('Mage', false)){
			require_once ($this->config ['path'] . 'app/Mage.php');
		}

		// disable Notices
		error_reporting ( E_ALL & ~ E_NOTICE );		

		// overwrite Magento Autoload Funktion
		if(class_exists('Varien_Autoload', false)){
			spl_autoload_unregister(array(Varien_Autoload::instance(), 'autoload'));
		}
		spl_autoload_register ( array (&$this, 'autoload' ) );
	}


	/**
	 * generate a duplicate of a Class with an other Name
	 * Mage_Core_Model_App -> Flagbit_Typo3connect_Rewrite_Core_Model_App
	 *
	 * @param string $className
	 * @return string
	 */
	protected function rewriteClass($className) {

		// cache Path
		$cachePath = $this->config['path'].'var/cache/';

		// get Filename from Classname
		$fileName = $this->_getClassPath($className);

		// generate a new Version of Classfile if not exists
		if(!file_exists($cachePath.$fileName)){

			// get source of the original Class
			$content = file_get_contents($this->config['path'].'app/code/core/'.uc_words ( $className, DS ) . '.php');

			// change Classname
			$content = preg_replace('/class(.*)'.$className.'/iU','class\1Flagbit_Typo3connect_Rewrite_' . $className, $content);

			// write new Class
			$classPath = substr($fileName, 0, strrpos($fileName, '/') + 1);

			t3lib_div::mkdir_deep($cachePath, substr($fileName, 0, strrpos($fileName, '/') + 1));
			t3lib_div::writeFile($cachePath.$fileName, $content);
		}

		return $cachePath.$fileName;
	}


	/**
	 * get absolute class path by class name
	 * 
	 * @param string $className
	 * @retirn string
	 */
	protected function _getClassPath($className) {

		$fileName = $this->_getFilename($className);

		if(substr($fileName,1,1) != '/'){
			$includePaths = explode(':',get_include_path());
			foreach($includePaths as $includePath){
				if($includePath == '.'){
					$includePath = rtrim($this->config['path'], '/');
				}
				if(file_exists($includePath.'/'.$fileName)){
					return $includePath.'/'.$fileName;
				}
			}
		}
	}


	/**
	 * get the Filename of a Class
	 *
	 * @param string $className
	 * @return string
	 */
	protected function _getFilename($className) {
		$filename = uc_words ( $className, DS) . '.php';
		return $filename;
	}


	/**
	 * Class autoload
	 *
	 * @todo change to spl_autoload_register
	 * @param string $class
	 */
	public function autoload($class) {

		if (strpos ( $class, DS ) !== false) {
			return;
		}

		// to some dirty Class reflection because of Mages unrewriteable Classes
		$filename = $this->_getFilename($class);
		$rewritePath = $this->config['path'].'app/code/'.$this->config['namespace'].'/Flagbit/Typo3connect/Rewrites/'.$filename;

		if(file_exists($rewritePath) 
			&& $filename != '.php' && $filename){

			$newClassFile = $this->rewriteClass($class);
			include($newClassFile);

			include($rewritePath);
			return;
		}

		$classFile = uc_words ( $class, DS ) . '.php';

		try{
			@include ($classFile);
		}catch (Exception $m){
			// no output since TYPO3 Classes will also be loaded through autoload
		}
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fb_magento/lib/class.tx_fbmagento_autoloader.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fb_magento/lib/class.tx_fbmagento_autoloader.php']);
}

?>