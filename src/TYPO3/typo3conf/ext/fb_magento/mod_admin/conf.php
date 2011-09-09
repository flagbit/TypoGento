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
 * Module config
 *
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
define('TYPO3_MOD_PATH', '../typo3conf/ext/fb_magento/mod_admin/');
$BACK_PATH='../../../../typo3/';

// for developing environment
if($_SERVER['SERVER_NAME'] == 'typogento-main.dev.local') {
	$BACK_PATH='/var/www/virtual/typogento/typogento-main/typo3/';
}
$MCONF['name']='txfbmagentoMgroup_txfbmagentoMadmin';

$MCONF['access']='user,group';
$MCONF['script']='index.php';

$MLANG['default']['tabs_images']['tab'] = 'moduleicon.gif';
$MLANG['default']['ll_ref']='LLL:EXT:fb_magento/mod_admin/locallang_mod.xml';
?>