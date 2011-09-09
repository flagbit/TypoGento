<?php
class Mage_Core_Model_App extends Flagbit_Typo3connect_Rewrite_Mage_Core_Model_App
{
    /**
     * Retrieve response object
     *
     * @return Zend_Controller_Response_Http
     */
    public function getResponse()
    {
        return Mage::getSingleton('Flagbit_Typo3connect/Core')->getResponse();
    }	

}
