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
 * TypoGento tools
 *
 * @version $Id$
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class tx_fbmagento_tools {
	
	/**
	 * returns the ExtConfig as Array
	 *
	 * @return array
	 */
	public static function getExtConfig(){
		return unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['fb_magento']);
	}
	
	/**
	 * throws an Exeption
	 *
	 * @param string $message
	 */
    public static function throwException($message){
    	
        throw new Exception($message);
    }	
	
    /**
     * displays an Error Message
     *
     * @param string $warning
     * @return unknown
     */
    public static function displayError($warning, $stop=true){
    	
    	$warning = '<h3>TYPOGENTO</h3>'.$warning;
    	
    	t3lib_BEfunc::typo3PrintError('', $warning, '', $stop ? 0 : 1);
		
    	if($stop) { 
			die();
		}
    }
    
	/**
	 * get Frontend Languagecode
	 *
	 * @return string
	 */
	public static function getFELangStoreCode(){
		//$GLOBALS['TYPO3_DB']->debugOutput = true;

		if(empty($GLOBALS['TSFE']->config['config']['sys_language_uid'])){
			if ($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_fbmagento_pi1.']['storeName']) {
				return $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_fbmagento_pi1.']['storeName'];
			}
			return 'default';
		}
		
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('tx_fbmagento_store', 'sys_language', sprintf('uid = %d', $GLOBALS['TSFE']->config['config']['sys_language_uid']));
		
		$res = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		if (! ($store = $res['tx_fbmagento_store'])) {
			if ($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_fbmagento_pi1.']['storeName']) {
				$store = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_fbmagento_pi1.']['storeName'];
			}
			else {
				$store = 'default';
			}
		}
		return $store;
	}
	
	
public static function typogentoErrorHandler($errno, $errstr, $errfile, $errline){
	    if (strpos($errstr, 'DateTimeZone::__construct')!==false) {
	        // there's no way to distinguish between caught system exceptions and warnings
	        return false;
	    }
	
	    $errno = $errno & error_reporting();
	    if ($errno == 0) {
	        return false;
	    }
	    if (!defined('E_STRICT')) {
	        define('E_STRICT', 2048);
	    }
	    if (!defined('E_RECOVERABLE_ERROR')) {
	        define('E_RECOVERABLE_ERROR', 4096);
	    }
	
	    // PEAR specific message handling
	    if (stripos($errfile.$errstr, 'pear') !== false) {
	         // ignore strict notices
	        if ($errno == E_STRICT) {
	            return false;
	        }
	        // ignore attempts to read system files when open_basedir is set
	        if ($errno == E_WARNING && stripos($errstr, 'open_basedir') !== false) {
	            return false;
	        }
	    }
	
	    $errorMessage = '';
	
	    switch($errno){
	        case E_ERROR:
	            $errorMessage .= "Error";
	            break;
	        case E_WARNING:
	            $errorMessage .= "Warning";
	            break;
	        case E_PARSE:
	            $errorMessage .= "Parse Error";
	            break;
	        
	        // No Exception on notices - TYPO3 is not as strict as Magento
	        case E_NOTICE:
	            return true;
	        case E_CORE_ERROR:
	            $errorMessage .= "Core Error";
	            break;
	        case E_CORE_WARNING:
	            $errorMessage .= "Core Warning";
	            break;
	        case E_COMPILE_ERROR:
	            $errorMessage .= "Compile Error";
	            break;
	        case E_COMPILE_WARNING:
	            $errorMessage .= "Compile Warning";
	            break;
	        case E_USER_ERROR:
	            $errorMessage .= "User Error";
	            break;
	        case E_USER_WARNING:
	            $errorMessage .= "User Warning";
	            break;
	        case E_USER_NOTICE:
	            $errorMessage .= "User Notice";
	            break;
	        case E_STRICT:
	            $errorMessage .= "Strict Notice";
	            break;
	        case E_RECOVERABLE_ERROR:
	            $errorMessage .= "Recoverable Error";
	            break;
	        default:
	            $errorMessage .= "Unknown error ($errno)";
	            break;
	    }
	
	    $errorMessage .= ": {$errstr}  in {$errfile} on line {$errline}";
	    throw new Exception($errorMessage);
	}
	
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fb_magento/lib/class.tx_fbmagento_tools.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/fb_magento/lib/class.tx_fbmagento_tools.php']);
}