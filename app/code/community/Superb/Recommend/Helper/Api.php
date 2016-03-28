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

class Superb_Recommend_Helper_Api extends Mage_Core_Helper_Data
{
    const XML_PATH_ENABLED                          = 'superbrecommend/general_settings/enabled';
    const XML_PATH_TRACKING_ACCOUNT_ID              = 'superbrecommend/general_settings/account_id';
    const XML_PATH_API_URL                          = 'superbrecommend/general_settings/api_url';
    const XML_PATH_API_USERNAME                     = 'superbrecommend/api_settings/username';
    const XML_PATH_API_KEY                          = 'superbrecommend/general_settings/api_key';
    const XML_PATH_API_ACCESS_TOKEN                 = 'superbrecommend/general_settings/api_access_token';
    const XML_PATH_API_SHOW_OUT_OF_STOCK_PRODUCTS   = 'superbrecommend/panels/show_out_of_stock_products';
    
    protected $_tokenData = array();

    protected function _getGetTokenUrl($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_API_URL,$storeId).'v1/'.urlencode(Mage::getStoreConfig(self::XML_PATH_TRACKING_ACCOUNT_ID,$storeId)).'/authenticate';
    }

    protected function _getUpdateAccountUrl($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_API_URL,$storeId).'v1/'.urlencode(Mage::getStoreConfig(self::XML_PATH_TRACKING_ACCOUNT_ID,$storeId)).'/update';
    }

    protected function _getUploadProductsDataUrl($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_API_URL,$storeId).'v1/'.urlencode(Mage::getStoreConfig(self::XML_PATH_TRACKING_ACCOUNT_ID,$storeId)).'/products/update';
    }

    protected function _getGetProductsPageviewsDataUrl($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_API_URL,$storeId).'v1/'.urlencode(Mage::getStoreConfig(self::XML_PATH_TRACKING_ACCOUNT_ID,$storeId)).'/products/pageviews';
    }

    protected function _getGetSlotsPageTypesDataUrl($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_API_URL,$storeId).'v1/'.urlencode(Mage::getStoreConfig(self::XML_PATH_TRACKING_ACCOUNT_ID,$storeId)).'/pagetypes';
    }

    protected function _getGetPanelsListDataUrl($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_API_URL,$storeId).'v1/'.urlencode(Mage::getStoreConfig(self::XML_PATH_TRACKING_ACCOUNT_ID,$storeId)).'/panels/search';
    }

    protected function _getGetProductAttributesListDataUrl($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_API_URL,$storeId).'v1/'.urlencode(Mage::getStoreConfig(self::XML_PATH_TRACKING_ACCOUNT_ID,$storeId)).'/productattributes/search';
    }

    protected function _getGetCustomerAttributesListDataUrl($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_API_URL,$storeId).'v1/'.urlencode(Mage::getStoreConfig(self::XML_PATH_TRACKING_ACCOUNT_ID,$storeId)).'/customerattributes/search';
    }

    protected function _getUpdateSlotsUrl($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_API_URL,$storeId).'v1/'.urlencode(Mage::getStoreConfig(self::XML_PATH_TRACKING_ACCOUNT_ID,$storeId)).'/slots/update';
    }

    protected function _getGetSlotsDataUrl($storeId = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_API_URL,$storeId).'v1/'.urlencode(Mage::getStoreConfig(self::XML_PATH_TRACKING_ACCOUNT_ID,$storeId)).'/slots';
    }

    protected function _getAccessToken($storeId = null)
    {
        if (!isset($this->_tokenData[$storeId]) || (is_array($this->_tokenData[$storeId]) && !isset($this->_tokenData[$storeId]['token'])) || (is_array($this->_tokenData[$storeId]) && isset($this->_tokenData[$storeId]['expires_date']) && (time()>$this->_tokenData[$storeId]['expires_date'])))
        {
            $ch = curl_init();
            $data_string = json_encode(array('key'=>Mage::helper('core')->decrypt(Mage::getStoreConfig(self::XML_PATH_API_KEY,$storeId))));
            curl_setopt($ch, CURLOPT_URL, $this->_getGetTokenUrl($storeId));
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            $headers = array();
            $headers[] = 'Content-Type: application/json';
            $headers[] = 'Content-Length: ' . strlen($data_string);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            try {
                $responseBody = curl_exec($ch);
                $tokenData = @json_decode($responseBody,true);
                if (isset($tokenData['success']) && $tokenData['success']==true)
                {
                    $this->_tokenData[$storeId] = $tokenData['token'];
                }
            } catch (Exception $e) {
                Mage::log($e->getMessage()."\n".$e->getTraceAsString(),null,'recommend-api.log');
            }
        }
        if (isset($this->_tokenData[$storeId]) && is_array($this->_tokenData[$storeId]) && isset($this->_tokenData[$storeId]['token']))
        {
            return $this->_tokenData[$storeId]['token'];
        }
    }

    public function uploadProductsData($products,$storeId = null)
    {
        $ch = curl_init();
        $data_string = json_encode(array('products'=>$products));
        curl_setopt($ch, CURLOPT_URL, $this->_getUploadProductsDataUrl($storeId));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        $headers = array();
        $headers[] = 'X-Auth-Token: '.$this->_getAccessToken($storeId);
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Content-Length: ' . strlen($data_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        try {
            $responseBody = curl_exec($ch);
            Mage::log($responseBody,null,'recommend-upload-products-data.log');
        } catch (Exception $e) {
            Mage::log($e->getMessage()."\n".$e->getTraceAsString(),null,'recommend-api.log');
        }
    }

    public function getProductsPageviewsData($hours,$storeId = null)
    {
        $ch = curl_init();
        $data_string = json_encode(array('hours'=>$hours));
        curl_setopt($ch, CURLOPT_URL, $this->_getGetProductsPageviewsDataUrl($storeId));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        $headers = array();
        $headers[] = 'X-Auth-Token: '.$this->_getAccessToken($storeId);
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Content-Length: ' . strlen($data_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        try {
            $responseBody = curl_exec($ch);
            Mage::log($responseBody,null,'recommend-get-products-pageviews-data.log');
            $response = json_decode($responseBody,true);
            if (isset($response['success']) && $response['success']==true && isset($response['products']) && is_array($response['products']))
            {
                $productsData = $response['products'];
                return $productsData;
            }
        } catch (Exception $e) {
            Mage::log($e->getMessage()."\n".$e->getTraceAsString(),null,'recommend-api.log');
        }
    }

    public function getSlotsPageTypesData($storeId = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->_getGetSlotsPageTypesDataUrl($storeId));
        $headers = array();
        $headers[] = 'X-Auth-Token: '.$this->_getAccessToken($storeId);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        try {
            $responseBody = curl_exec($ch);
            Mage::log($responseBody,null,'recommend-get-slots-page-types-data.log');
            $response = json_decode($responseBody,true);
            if (isset($response['success']) && $response['success']==true && isset($response['results']) && is_array($response['results']))
            {
                $slotsPageTypesData = $response['results'];
                return $slotsPageTypesData;
            }
        } catch (Exception $e) {
            Mage::log($e->getMessage()."\n".$e->getTraceAsString(),null,'recommend-api.log');
        }
    }

    public function getSlotsData($storeId = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->_getGetSlotsDataUrl($storeId));
        $headers = array();
        $headers[] = 'X-Auth-Token: '.$this->_getAccessToken($storeId);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        try {
            $responseBody = curl_exec($ch);
            Mage::log($responseBody,null,'recommend-get-slots-data.log');
            $response = json_decode($responseBody,true);
            if (isset($response['success']) && $response['success']==true && isset($response['results']) && is_array($response['results']))
            {
                $slotsData = $response['results'];
                return $slotsData;
            }
        } catch (Exception $e) {
            Mage::log($e->getMessage()."\n".$e->getTraceAsString(),null,'recommend-api.log');
        }
    }

    public function updateSlots($slotsData,$storeId = null)
    {
        $ch = curl_init();
        $data_string = json_encode(array('slots'=>$slotsData));
        curl_setopt($ch, CURLOPT_URL, $this->_getUpdateSlotsUrl($storeId));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        $headers = array();
        $headers[] = 'X-Auth-Token: '.$this->_getAccessToken($storeId);
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Content-Length: ' . strlen($data_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        try {
            $responseBody = curl_exec($ch);
            Mage::log($responseBody,null,'recommend-update-slots.log');
            $response = json_decode($responseBody,true);
            if (isset($response['success']) && $response['success']==true)
                return true;
        } catch (Exception $e) {
            Mage::log($e->getMessage()."\n".$e->getTraceAsString(),null,'recommend-api.log');
        }
    }

    public function getPanelsListData($storeId = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->_getGetPanelsListDataUrl($storeId));
        $headers = array();
        $headers[] = 'X-Auth-Token: '.$this->_getAccessToken($storeId);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        try {
            $responseBody = curl_exec($ch);
            Mage::log($responseBody,null,'recommend-get-panels-list-data.log');
            $response = json_decode($responseBody,true);
            if (isset($response['success']) && $response['success']==true && isset($response['results']) && is_array($response['results']))
            {
                $panelsData = $response['results'];
                return $panelsData;
            }
        } catch (Exception $e) {
            Mage::log($e->getMessage()."\n".$e->getTraceAsString(),null,'recommend-api.log');
        }
    }

    public function getProductAttributesListData($storeId = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->_getGetProductAttributesListDataUrl($storeId));
        $headers = array();
        $headers[] = 'X-Auth-Token: '.$this->_getAccessToken($storeId);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        try {
            $responseBody = curl_exec($ch);
            Mage::log($responseBody,null,'recommend-get-product-attributes-list-data.log');
            $response = json_decode($responseBody,true);
            if (isset($response['success']) && $response['success']==true && isset($response['results']) && is_array($response['results']))
            {
                $panelsData = $response['results'];
                return $panelsData;
            }
        } catch (Exception $e) {
            Mage::log($e->getMessage()."\n".$e->getTraceAsString(),null,'recommend-api.log');
        }
    }

    public function getCustomerAttributesListData($storeId = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->_getGetCustomerAttributesListDataUrl($storeId));
        $headers = array();
        $headers[] = 'X-Auth-Token: '.$this->_getAccessToken($storeId);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        try {
            $responseBody = curl_exec($ch);
            Mage::log($responseBody,null,'recommend-get-customer-attributes-list-data.log');
            $response = json_decode($responseBody,true);
            if (isset($response['success']) && $response['success']==true && isset($response['results']) && is_array($response['results']))
            {
                $panelsData = $response['results'];
                return $panelsData;
            }
        } catch (Exception $e) {
            Mage::log($e->getMessage()."\n".$e->getTraceAsString(),null,'recommend-api.log');
        }
    }

    public function updateAccount($storeId = null)
    {
        $ch = curl_init();
        $data_string = json_encode(array('currency'=>Mage::app()->getStore($storeId)->getBaseCurrencyCode(),'platform'=>'magento','platform_version'=>Mage::getVersion()));
        curl_setopt($ch, CURLOPT_URL, $this->_getUpdateAccountUrl($storeId));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        $headers = array();
        $headers[] = 'X-Auth-Token: '.$this->_getAccessToken($storeId);
        $headers[] = 'Content-Type: application/json';
        $headers[] = 'Content-Length: ' . strlen($data_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        try {
            $responseBody = curl_exec($ch);
            Mage::log($responseBody,null,'recommend-update-account.log');
            $response = json_decode($responseBody,true);
            if (isset($response['success']) && $response['success']==true)
                return true;
            elseif (isset($response['error']) && $response['error']==true && isset($response['error_message']) && $response['error_message']=='Base currency can not be changed after build.')
            {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Once you have posted transactions and accounts using the base currency, you cannot change the base currency.'));
                return false;
            }
            elseif (isset($response['error']) && isset($response['error_message']) && $response['error_message']=='Access denied')
            {
                Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('API not connected. Check Account Id and API key.'));
                return false;
            }
        } catch (Exception $e) {
            Mage::log($e->getMessage()."\n".$e->getTraceAsString(),null,'recommend-api.log');
        }
    }

    public function getShowOutOfStockProduct(){
        return (bool) Mage::getStoreConfig(self::XML_PATH_API_SHOW_OUT_OF_STOCK_PRODUCTS);
    }
}
