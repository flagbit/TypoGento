<?php

require_once ('app/code/core/Mage/Customer/Model/Entity/Customer.php');

class Flagbit_Typo3connect_Model_Entity_Customer extends Mage_Eav_Model_Entity_Abstract {
	
    public function __construct()
    {
        $resource = Mage::getSingleton('core/resource');
        $this->setType('Flagbit_Typo3connect');
        $this->setConnection(
            $resource->getConnection('typo3connect_read'),
            $resource->getConnection('typo3connect_write')
        );
    }

    /**
     * Retrieve customer entity default attributes
     *
     * @return array
     */
    protected function _getDefaultAttributes()
    {
    	return array();
        return array(
            'entity_type_id',
            'attribute_set_id',
            'created_at',
            'updated_at',
            'increment_id',
            'store_id',
            'website_id'
        );
    }
    
    
    protected function _getCmsDefaultAttributes(){
    	return array(
    		'firstname' => 'firstname',
    		'lastname' => 'name',
    		'password' => 'password'
    	);
    	
    }
    
    /**
     * Update entity attribute value
     *
     * @param   Varien_Object $object
     * @param   Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param   mixed $valueId
     * @param   mixed $value
     * @return  Mage_Eav_Model_Entity_Abstract
     */
    protected function _updateAttribute($object, $attribute, $valueId, $value)
    {
    	
    	// save cms Database Details
		$cmsWrite = $this->_write;
		$cmsRead = $this->_read;
		$cmsEntityIdField = $this->_entityIdField;

    	$this->_entityIdField = 'entity_id';
    	$resource = Mage::getSingleton('core/resource');
    	$this->_write = $resource->getConnection('core_write');
    	$this->_read = $resource->getConnection('core_read');
    
    	parent::_updateAttribute($object, $attribute, $valueId, $value);
    	
    	$this->_entityIdField = $cmsEntityIdField;
    	$this->_write = $cmsWrite;
    	$this->_read = $cmsRead;

    	return $this;
    }

    /**
     * Prepare entity object data for save
     *
     * result array structure:
     * array (
     *  'newObject', 'entityRow', 'insert', 'update', 'delete'
     * )
     *
     * @param   Varien_Object $newObject
     * @return  array
     */
    protected function _collectSaveData($newObject)
    {
        $newData = $newObject->getData();
        $entityId = $newObject->getData($this->getEntityIdField());
        if (!empty($entityId)) {
            /**
             * get current data in db for this entity
             */
            /*$className  = get_class($newObject);
            $origObject = new $className();
            $origObject->setData(array());
            $this->load($origObject, $entityId);
            $origData = $origObject->getOrigData();*/
            $origData = $this->_getOrigObject($newObject)->getOrigData();

            /**
             * drop attributes that are unknown in new data
             * not needed after introduction of partial entity loading
             */
            foreach ($origData as $k=>$v) {
                if (!array_key_exists($k, $newData)) {
                    unset($origData[$k]);
                }
            }
        }
        $cmsFields = $this->_getCmsDefaultAttributes();
        $cmsUpdate = array();
        
        //var_dump($newData);
        foreach ($newData as $k=>$v) {
            /**
             * Check attribute information
             */
            if (is_numeric($k) || is_array($v)) {
                continue;
                throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Invalid data object key'));
            }

            $attribute = $this->getAttribute($k);
            
            if (empty($attribute)) {
            	if(isset($cmsFields[$k])){
           			$cmsUpdate[$cmsFields[$k]] = $v;
           		}
                continue;
            }

            $attrId = $attribute->getAttributeId();

            /**
             * if attribute is static add to entity row and continue
             */
            if ($this->isAttributeStatic($k)) {
                $entityRow[$k] = $this->_prepareStaticValue($k, $v);
                continue;
            }

            /**
             * Check comparability for attribute value
             */
            if (isset($origData[$k])) {
                if ($attribute->isValueEmpty($v)) {
                    $delete[$attribute->getBackend()->getTable()][] = array(
                        'attribute_id'  => $attrId,
                        'value_id'      => $attribute->getBackend()->getValueId()
                    );
                }
                elseif ($v!==$origData[$k]) {
                    $update[$attrId] = array(
                        'value_id' => $attribute->getBackend()->getValueId(),
                        'value'    => $v,
                    );
                }
            }
            elseif (!$attribute->isValueEmpty($v)) {
                $insert[$attrId] = $v;
            }
        }

        $result = compact('newObject', 'entityRow', 'insert', 'update', 'delete', 'cmsUpdate');
        return $result;
    }
    
    /**
     * Save object collected data
     *
     * @param   array $saveData array('newObject', 'entityRow', 'insert', 'update', 'delete')
     * @return  Mage_Eav_Model_Entity_Abstract
     */
    protected function _processSaveData($saveData)
    {
    	extract($saveData);
    	if(isset($cmsUpdate)){
    		
    		$resource = Mage::getSingleton('core/resource');
    		
    		/*@var $r Varien_Db_Adapter_Pdo_Mysql */
    		$r = $resource->getConnection('typo3connect_write');
    		
    		if($newObject->getId()){
				$r->update('fe_users',$cmsUpdate, 'uid='.$newObject->getId());
    		}else{
    			$r->insert('fe_users', $cmsUpdate);
    			$newObject->setId($r->lastInsertId('fe_users', 'uid'));
    		}
    	}
    	
    	return parent::_processSaveData(compact('newObject', 'entityRow', 'insert', 'update', 'delete'));
    }    
    
    
    /**
     * Insert entity attribute value
     *
     * @param   Varien_Object $object
     * @param   Mage_Eav_Model_Entity_Attribute_Abstract $attribute
     * @param   mixed $value
     * @return  Mage_Eav_Model_Entity_Abstract
     */
    protected function _insertAttribute($object, $attribute, $value)
    {
        $entityIdField = $attribute->getBackend()->getEntityIdField();
        $row = array(
            $entityIdField  => $object->getId(),
            'entity_type_id'=> $object->getEntityTypeId(),
            'attribute_id'  => $attribute->getId(),
            'value'         => $this->_prepareValueForSave($value, $attribute),
        );
        
        $resource = Mage::getSingleton('core/resource');
        $w = $resource->getConnection('customer_write');
        $w->insert($attribute->getBackend()->getTable(), $row);
        
        // $this->_getWriteAdapter()->insert($attribute->getBackend()->getTable(), $row);
        return $this;
    }   

    /**
     * Retrieve select object for loading entity attributes values
     *
     * @param   Varien_Object $object
     * @param   mixed $rowId
     * @return  Zend_Db_Select
     */
    protected function _getLoadAttributesSelect($object, $table)
    {
        $select = $this->_read->select()
            ->from($table)
            ->where('entity_id' . '=?', $object->getId());
        return $select;
    }    

    
    /**
     * Load entity's attributes into the object
     *
     * @param   Varien_Object $object
     * @param   integer $entityId
     * @param   array|null $attributes
     * @return  Mage_Eav_Model_Entity_Abstract
     */
    public function load($object, $entityId, $attributes=array())
    {
        /**
         * Load object base row data
         */
        $select = $this->_getLoadRowSelect($object, $entityId);
        $row = $this->_getReadAdapter()->fetchRow($select);
        //$object->setData($row);
        if (is_array($row)) {
            $object->addData($row);
        }

        if (empty($attributes)) {
            $this->loadAllAttributes($object);
        } else {
            foreach ($attributes as $attrCode) {
                $this->getAttribute($attrCode);
            }
        }

        /**
         * Load data for entity attributes
         */
        $resource = Mage::getSingleton('core/resource');
        $r = $resource->getConnection('core_read');
        foreach ($this->getAttributesByTable() as $table=>$attributes) {
            $select = $this->_getLoadAttributesSelect($object, $table);
            $values = $r->fetchAll($select);

            foreach ($values as $valueRow) {
                $this->_setAttribteValue($object, $valueRow);
            }
        }

        $object->setOrigData();
        $this->_afterLoad($object);
        return $this;
    }   
    

    protected function _beforeSave(Varien_Object $customer)
    {
        parent::_beforeSave($customer);
        $select = $this->_getReadAdapter()->select()
            ->from($this->getEntityTable(), array($this->getEntityIdField()))
            ->where('email=?', $customer->getEmail());
        if ($customer->getSharingConfig()->isWebsiteScope()) {
            #$select->where('website_id=?', (int) $customer->getWebsiteId());
        }
        if ($customer->getId()) {
            $select->where('uid !=?', $customer->getId());
        }

        if ($this->_getWriteAdapter()->fetchOne($select)) {
            Mage::throwException(Mage::helper('customer')->__('Customer email already exists'));
        }

        // set confirmation key logic
        if ($customer->getForceConfirmed()) {
            $customer->setConfirmation(null);
        }
        elseif ((!$customer->getId()) && ($customer->isConfirmationRequired())) {
            $customer->setConfirmation($customer->getRandomConfirmationKey());
        }
        // remove customer confirmation key from database, if empty
        if (!$customer->getConfirmation()) {
            $customer->setConfirmation(null);
        }

        return $this;
    }

    /**
     * Save customer addresses and set default addresses in attributes backend
     *
     * @param   Varien_Object $customer
     * @return  Mage_Eav_Model_Entity_Abstract
     */
    protected function _afterSave(Varien_Object $customer)
    {
        $this->_saveAddresses($customer);
        return parent::_afterSave($customer);
    }


    protected function _saveAddresses(Mage_Customer_Model_Customer $customer)
    {
        foreach ($customer->getAddresses() as $address) {
            if ($address->getData('_deleted')) {
                $address->delete();
            }
            else {
                $address->setParentId($customer->getId())
                    ->setStoreId($customer->getStoreId())
                    ->save();
            }
        }
        return $this;
    }

    /**
     * Retrieve select object for loading base entity row
     *
     * @param   Varien_Object $object
     * @param   mixed $rowId
     * @return  Zend_Db_Select
     */
    protected function _getLoadRowSelect($object, $rowId)
    {
        $select = parent::_getLoadRowSelect($object, $rowId);
        if ($object->getWebsiteId() && $object->getSharingConfig()->isWebsiteScope()) {
            #$select->where('website_id=?', (int) $object->getWebsiteId());
        }
        return $select;
    }

    public function loadByEmail(Mage_Customer_Model_Customer $customer, $email, $testOnly=false)
    {
        $select = $this->_getReadAdapter()->select()
            ->from($this->getEntityTable(), array($this->getEntityIdField()))
            //->where('email=?', $email);
            ->where('email=:customer_email');
        if ($customer->getSharingConfig()->isWebsiteScope()) {
            //if (!$customer->hasData('website_id')) {
            //    Mage::throwException(Mage::helper('customer')->__('Customer website id must be specified, when using website scope.'));
            //}
            #$select->where('website_id=?', (int)$customer->getWebsiteId());
        }

        if ($id = $this->_getReadAdapter()->fetchOne($select, array('customer_email' => $email))) {
            $this->load($customer, $id);
        }
        else {
            $customer->setData(array());
        }
        return $this;
    }

    /**
     * Change customer password
     * $data = array(
     *      ['password']
     *      ['confirmation']
     *      ['current_password']
     * )
     *
     * @param   Mage_Customer_Model_Customer
     * @param   array $data
     * @param   bool $checkCurrent
     * @return  this
     */
    public function changePassword(Mage_Customer_Model_Customer $customer, $newPassword, $checkCurrent=true)
    {
        $customer->setPassword($newPassword);
        $this->saveAttribute($customer, 'password_hash');
        return $this;
    }
	
}

?>