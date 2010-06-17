<?php

class Flagbit_Typo3connect_Block_Customer_Edit_Form extends Mage_Adminhtml_Block_Customer_Group_Edit_Form {
	

    /**
     * Prepare form for render
     */
    protected function _prepareLayout()
    {
        Mage_Adminhtml_Block_Widget_Form::_prepareLayout();
        $form = new Varien_Data_Form();
        $customerGroup = Mage::registry('current_group');

        $fieldset = $form->addFieldset('base_fieldset', array('legend'=>Mage::helper('customer')->__('Group Information')));

        $name = $fieldset->addField('customer_group_code', 'text',
            array(
                'name'  => 'code',
                'label' => Mage::helper('customer')->__('Group Name'),
                'title' => Mage::helper('customer')->__('Group Name'),
                'class' => 'required-entry',
                'required' => true,
            )
        );

        if ($customerGroup->getId()==0 && $customerGroup->getCustomerGroupCode() ) {
            $name->setDisabled(true);
        }

        $fieldset->addField('tax_class_id', 'select',
            array(
                'name'  => 'tax_class',
                'label' => Mage::helper('customer')->__('Tax class'),
                'title' => Mage::helper('customer')->__('Tax class'),
                'class' => 'required-entry',
                'required' => true,
                'values' => Mage::getSingleton('tax/class_source_customer')->toOptionArray()
            )
        );
        
        $fieldset->addField('typo3_group_id', 'select',
            array(
                'name'  => 'typo3_group_id',
                'label' => Mage::helper('customer')->__('TYPO3 Group'),
                'title' => Mage::helper('customer')->__('TYPO3 Group'),
                'required' => false,
                'values' => array_merge(array('' => $this->__('none')), Mage::getResourceModel('Flagbit_Typo3connect/typo3_frontend_group_collection')->addFieldToFilter('deleted', '0')->load()->toOptionArray())
            )
        );       

        if (!is_null($customerGroup->getId())) {
            // If edit add id
            $form->addField('id', 'hidden',
                array(
                    'name'  => 'id',
                    'value' => $customerGroup->getId(),
                )
            );
        }

        if( Mage::getSingleton('adminhtml/session')->getCustomerGroupData() ) {
            $form->addValues(Mage::getSingleton('adminhtml/session')->getCustomerGroupData());
            Mage::getSingleton('adminhtml/session')->setCustomerGroupData(null);
        } else {
            $form->addValues($customerGroup->getData());
        }

        $form->setUseContainer(true);
        $form->setId('edit_form');
        $form->setAction($this->getUrl('*/*/save'));
        $this->setForm($form);
    }	
	
}