<?php
/*
 * Superb_Recommend
 * 
 * NOTICE OF LICENSE
 * 
 * This source file is subject to the Open Software License (OSL 3.0), a
 * copy of which is available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * 
 * @category   Superb
 * @package    Superb_Recommend
 * @author     Superb <hello@wearesuperb.com>
 * @copyright  Copyright (c) 2015 Superb Media Limited
 * @license    http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */

class Superb_Recommend_Helper_Admin extends Mage_Core_Helper_Data
{
    static protected $_slotSystemConfigSaveData = array();
    static protected $_slotsData = array();
    static protected $_panelsData = array();
    static protected $_productAttributesData = array();
    static protected $_customerAttributesData = array();
    static protected $_systemConfigStoreId = -1;

    public function setSaveSlotSystemConfig($slotId, $data)
    {
        self::$_slotSystemConfigSaveData[$slotId] = $data;
    }

    public function getSaveSlotSystemConfig()
    {
        return self::$_slotSystemConfigSaveData;
    }

    public function getSlotsData($storeId = null)
    {
        if (!isset(self::$_slotsData[$storeId]))
        {
            self::$_slotsData[$storeId] = Mage::helper('superbrecommend/api')->getSlotsData($storeId);
        }
        return self::$_slotsData[$storeId];
    }

    public function getPanelsListData($storeId = null)
    {
        if (!isset(self::$_panelsData[$storeId]))
        {
            self::$_panelsData[$storeId] = Mage::helper('superbrecommend/api')->getPanelsListData($storeId);
        }
        return self::$_panelsData[$storeId];
    }

    public function getProductAttributesListData($storeId = null)
    {
        if (!isset(self::$_productAttributesData[$storeId]))
        {
            self::$_productAttributesData[$storeId] = Mage::helper('superbrecommend/api')->getProductAttributesListData($storeId);
        }
        return self::$_productAttributesData[$storeId];
    }

    public function getCustomerAttributesListData($storeId = null)
    {
        if (!isset(self::$_customerAttributesData[$storeId]))
        {
            self::$_customerAttributesData[$storeId] = Mage::helper('superbrecommend/api')->getCustomerAttributesListData($storeId);
        }
        return self::$_customerAttributesData[$storeId];
    }

    public function getSystemConfigStoreId()
    {
        if (self::$_systemConfigStoreId==-1)
        {
            if (strlen($this->getRequest()->getParam('store')))
                self::$_systemConfigStoreId = $this->getRequest()->getParam('store');
            elseif (strlen($this->getRequest()->getParam('website')))
                self::$_systemConfigStoreId = Mage::getModel('core/website')->load($this->getRequest()->getParam('website'))->getDefaultStore()->getId();
            else
                self::$_systemConfigStoreId = null;
        }
        return self::$_systemConfigStoreId;
    }

    /**
     * Retrieve request object
     *
     * @return Mage_Core_Controller_Request_Http
     * @throws Exception
     */
    public function getRequest()
    {
        $controller = Mage::app()->getFrontController();
        if ($controller) {
            $this->_request = $controller->getRequest();
        } else {
            throw new Exception(Mage::helper('core')->__("Can't retrieve request object"));
        }
        return $this->_request;
    }

    public function isSingleMode()
    {
        return Mage::getStoreConfig(Superb_Recommend_Helper_Api::XML_PATH_TRACKING_ACCOUNT_ID,$this->getSystemConfigStoreId())==Mage::getStoreConfig(Superb_Recommend_Helper_Api::XML_PATH_TRACKING_ACCOUNT_ID);
    }

    public function isStoreMode()
    {
        return strlen($this->getRequest()->getParam('store'));
    }
}
