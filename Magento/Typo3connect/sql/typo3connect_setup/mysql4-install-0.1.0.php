<?php 


$installer = $this;
/* @var $installer Mage_Core_Model_Resource_Setup */

$installer->startSetup();
/*
$installer->run("

INSERT INTO {$this->getTable('eav_entity_type')} (`entity_type_id`, `entity_type_code`, `entity_model`, `attribute_model`, `entity_table`, `value_table_prefix`, `entity_id_field`, `is_data_sharing`, `data_sharing_key`, `default_attribute_set_id`, `increment_model`, `increment_per_store`, `increment_pad_length`, `increment_pad_char`) VALUES
(32, 'Flagbit_Typo3connect', 'Flagbit_Typo3connect/customer', '', 'Flagbit_Typo3connect/customer', 'customer_enity', 'uid', 1, 'default', 0, '', 0, 8, '0');

INSERT INTO {$this->getTable('eav_attribute')} (`attribute_id`, `entity_type_id`, `attribute_code`, `attribute_model`, `backend_model`, `backend_type`, `backend_table`, `frontend_model`, `frontend_input`, `frontend_label`, `frontend_class`, `source_model`, `is_global`, `is_visible`, `is_required`, `is_user_defined`, `default_value`, `is_searchable`, `is_filterable`, `is_comparable`, `is_visible_on_front`, `is_unique`, `is_visible_in_advanced_search`, `is_configurable`, `apply_to`, `position`, `note`, `is_used_for_price_rules`) VALUES (868, 32, 'default_shipping', NULL, 'customer/customer_attribute_backend_shipping', 'int', 'customer_address_entity', NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 0, NULL, 0, 0, 0, 0, 0, 0, 1, '', 0, '', 1);
INSERT INTO {$this->getTable('eav_attribute')} (`attribute_id`, `entity_type_id`, `attribute_code`, `attribute_model`, `backend_model`, `backend_type`, `backend_table`, `frontend_model`, `frontend_input`, `frontend_label`, `frontend_class`, `source_model`, `is_global`, `is_visible`, `is_required`, `is_user_defined`, `default_value`, `is_searchable`, `is_filterable`, `is_comparable`, `is_visible_on_front`, `is_unique`, `is_visible_in_advanced_search`, `is_configurable`, `apply_to`, `position`, `note`, `is_used_for_price_rules`) VALUES (869, 32, 'default_billing', NULL, 'customer/customer_attribute_backend_billing', 'int', 'customer_address_entity', NULL, NULL, NULL, NULL, NULL, 1, 1, 0, 0, NULL, 0, 0, 0, 0, 0, 0, 1, '', 0, '', 1);


");
*/

$installer->endSetup();
