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
 * TypoGento Installer
 *
 * @version $Id: mysql4-install-0.1.0.php 19 2008-11-25 17:50:44Z weller $
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */

$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();
$installer->run("
INSERT INTO `magento`.`eav_attribute` (
`attribute_id` ,
`entity_type_id` ,
`attribute_code` ,
`attribute_model` ,
`backend_model` ,
`backend_type` ,
`backend_table` ,
`frontend_model` ,
`frontend_input` ,
`frontend_label` ,
`frontend_class` ,
`source_model` ,
`is_global` ,
`is_visible` ,
`is_required` ,
`is_user_defined` ,
`default_value` ,
`is_searchable` ,
`is_filterable` ,
`is_comparable` ,
`is_visible_on_front` ,
`is_unique` ,
`is_visible_in_advanced_search` ,
`is_configurable` ,
`apply_to` ,
`position` ,
`note` ,
`is_used_for_price_rules`
)
VALUES (
NULL , '1', 'typo3_uid', NULL , '', 'int', ' ', '', 'hidden', '', '', '', '1', '1', '0', '0', '', '0', '0', '0', '0', '0', '0', '0', '', '0', '', '1'
);

");

$installer->endSetup();