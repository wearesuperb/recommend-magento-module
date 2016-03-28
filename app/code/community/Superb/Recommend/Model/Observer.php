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

class Superb_Recommend_Model_Observer
{
    CONST LIMIT_STEP = 1000;

    /**
     * Track after customer register
     *
     */
    public function customerRegisterAndUpdate($observer)
    {
        $helper = Mage::helper('superbrecommend');
        if (!$helper->isEnabled()) {
            return $this;
        }
        /* @var $customer Mage_Customer_Model_Customer */
        $customer = $observer->getEvent()->getCustomer();
        if ($customer->isObjectNew()) {
            try {
                $data = $helper->getCustomerRegistrationConfirmData($customer);
                $helper->setTrackingData($data);
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
        else {
            try {
                $data = $helper->getCustomerUpdateDetailsData($customer);
                $helper->setTrackingData($data);
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
        return $this;
    }

    public function customerLogin()
    {
        $helper = Mage::helper('superbrecommend');
        if (!$helper->isEnabled()) {
            return $this;
        }
        try {
            $data = $helper->getCustomerLoginData();
            $helper->setTrackingData($data);
            $data = $helper->getCustomerCustomData();
            if (count($data))
            {
                foreach($data as $row)
                {
                    $helper->setTrackingData($row);
                }
            }
        } catch (Exception $e) {
            Mage::logException($e);
        }
    }

    public function productView($observer)
    {
        $helper = Mage::helper('superbrecommend');
        if (!$helper->isEnabled()) {
            return $this;
        }
        $product = $observer->getEvent()->getProduct();
        try {
            $data = $helper->getProductViewData($product);
            $helper->setTrackingData($data,true);
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return $this;
    }

    public function categoryView($observer)
    {
        $helper = Mage::helper('superbrecommend');
        if (!$helper->isEnabled()) {
            return $this;
        }
        $category = $observer->getEvent()->getCategory();
        try {
            $data = $helper->getCategoryViewData($category);
            $helper->setTrackingData($data,true);
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return $this;
    }

    public function cartSave($observer)
    {
        $helper = Mage::helper('superbrecommend');
        if (!$helper->isEnabled()) {
            return $this;
        }
        /* @var $cart Mage_Checkout_Model_Cart */
        $cart = $observer->getEvent()->getCart();
        try {
            $data = $helper->getCartStatusData($cart);
            $helper->setTrackingData($data);
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return $this;
    }

    public function subscriberSubscribed($observer)
    {
        $helper = Mage::helper('superbrecommend');
        if (!$helper->isEnabled()) {
            return $this;
        }
        $_subscriber = $observer->getEvent()->getSubscriber();
        try {
            $data = $helper->getCustomerSubscribeData($_subscriber->getEmail());
            $helper->setTrackingData($data);
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return $this;
    }
    
    public function subscriberUnsubscribed($observer)
    {
        $helper = Mage::helper('superbrecommend');
        if (!$helper->isEnabled()) {
            return $this;
        }
        $_subscriber = $observer->getEvent()->getSubscriber();
        try {
            $data = $helper->getCustomerUnsubscribeData($_subscriber->getEmail());
            $helper->setTrackingData($data);
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return $this;
    }

    public function wishlistUpdated($observer)
    {
        $helper = Mage::helper('superbrecommend');
        if (!$helper->isEnabled()) {
            return $this;
        }
        try {
            $data = $helper->getWishlistUpdatedData();
            $helper->setTrackingData($data);
        } catch (Exception $e) {
            Mage::logException($e);
        }
        return $this;
    }

    public function checkSubscription($observer)
    {
        $helper = Mage::helper('superbrecommend');
        if (!$helper->isEnabled()) {
            return $this;
        }
        $subscriber = $observer->getEvent()->getSubscriber();

        if (($subscriber->getOrigData('subscriber_status') != $subscriber->getData('subscriber_status')))
        {
           if ($subscriber->getData('subscriber_status')==Mage_Newsletter_Model_Subscriber::STATUS_UNSUBSCRIBED)
           {
                try {
                    $data = $helper->getCustomerUnsubscribeData($subscriber->getData('subscriber_email'));
                    $helper->setTrackingData($data);
                } catch (Exception $e) {
                    Mage::logException($e);
                }
           }
           if ($subscriber->getData('subscriber_status')==Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED)
           {
                try {
                    $data = $helper->getCustomerSubscribeData($subscriber->getData('subscriber_email'));
                    $helper->setTrackingData($data);
                } catch (Exception $e) {
                    Mage::logException($e);
                }
           }
        }
    }

    public function deleteSubscription($observer)
    {
        $helper = Mage::helper('superbrecommend');
        if (!$helper->isEnabled()) {
            return $this;
        }
        $subscriber = $observer->getEvent()->getSubscriber();

        if ($subscriber->getData('subscriber_status')==Mage_Newsletter_Model_Subscriber::STATUS_SUBSCRIBED)
        {
            try {
                $data = $helper->getCustomerUnsubscribeData($subscriber->getData('subscriber_email'));
                $helper->setTrackingData($data);
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
    }

    public function convertQuoteItemToOrderItem($observer)
    {
        $helper = Mage::helper('superbrecommend');
        if (!$helper->isEnabled()) {
            return $this;
        }
        $item = $observer->getEvent()->getItem();
        $orderItem = $observer->getEvent()->getOrderItem();
        $productOptions = $orderItem->getProductOptions();
        $productOptions['recommend-product-view-sku'] = $item->getProduct()->getData('sku');
        $orderItem->setProductOptions($productOptions);
    }

    public function updateProductsData(Mage_Cron_Model_Schedule $schedule)
    {
        $helper = Mage::helper('superbrecommend');
        if (!$helper->isEnabled()) {
            return $this;
        }
        $helper = Mage::helper('superbrecommend');
        if (!$helper->isDataCronEnabled()) {
            return $this;
        }
        $apiHelper = Mage::helper('superbrecommend/api');
        $products = array();

        Mage::app()->setCurrentStore('admin');
        $storesByAccounts = array();
        foreach (Mage::app()->getStores() as $store)
        {
            if (!$helper->isEnabled($store->getId()))
                continue;
            $accountId = Mage::getStoreConfig(Superb_Recommend_Helper_Api::XML_PATH_TRACKING_ACCOUNT_ID,$store->getId());
            if (!isset($storesByAccounts[$accountId]))
                $storesByAccounts[$accountId] = array();
            $storesByAccounts[$accountId][] = $store->getId();
        }
        foreach (Mage::app()->getStores() as $store)
        {
            if (!$helper->isEnabled($store->getId()))
                continue;
            $accountId = Mage::getStoreConfig(Superb_Recommend_Helper_Api::XML_PATH_TRACKING_ACCOUNT_ID,$store->getId());
            Mage::app()->setCurrentStore($store);
            Mage::unregister('_resource_singleton/catalog/product_flat');
            $currencies = array(); 
            $codes = Mage::app()->getStore()->getAvailableCurrencyCodes(true);
            if (is_array($codes)) {
                $rates = Mage::getModel('directory/currency')->getCurrencyRates(
                    Mage::app()->getStore()->getBaseCurrency(),
                    $codes
                );

                foreach ($codes as $code) {
                    if (isset($rates[$code])) {
                        $currencies[$code] = Mage::getModel('directory/currency')->load($code);
                    }
                    elseif($code==Mage::app()->getStore()->getBaseCurrency()->getCode())
                    {
                        $currencies[$code] = Mage::getModel('directory/currency')->load($code);
                    }
                }
            }

            $_attributes = array();

            foreach ($helper->getProductUpdateAttributes() as $row)
            {
               $_attributes[] = $row['magento_attribute'];
            }
            $eavConfig = Mage::getSingleton('eav/config');
            $collection = Mage::getResourceModel('catalog/product_collection')->setStore($store)->setStoreId($store->getStoreId());
            foreach($_attributes as $_attributeCode)
            {
                $attribute = $eavConfig->getAttribute('catalog_product', $_attributeCode);
                if ((int)$attribute->getData('used_in_product_listing'))
                    $collection->addAttributeToSelect($_attributeCode);
                else
                    $collection->joinAttribute(
                        $_attributeCode,
                        'catalog_product/'.$_attributeCode,
                        'entity_id',
                        null,
                        'left',
                        $store->getId()
                    );
            }
            $collection
                ->addAttributeToSelect(Mage::getSingleton('catalog/config')->getProductAttributes())
                ->addMinimalPrice()
                ->addFinalPrice()
                ->addTaxPercents()
                ->addStoreFilter($store)
                ->addUrlRewrite();

            Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
            Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
            if (!$apiHelper->getShowOutOfStockProduct($store->getStoreId())) {
                Mage::getSingleton('cataloginventory/stock_status')->addIsInStockFilterToCollection($collection);
            }

            $isEmpty = false;
            $offset = 0;
            while (!$isEmpty) {
                $productCol = clone $collection;
                $productCol->getSelect()->limit(self::LIMIT_STEP, $offset);

                $isEmpty = true;
                foreach($productCol as $product)
                {
                    $isEmpty = false;
                    $offset++;
                    if (!isset($products[$accountId]))
                        $products[$accountId] = array();
                    if (isset($products[$accountId][$product->getSku()]))
                    {
                        $finalPrice = $products[$accountId][$product->getSku()]['price'];
                        $price = $products[$accountId][$product->getSku()]['original_price'];
                    }
                    else
                    {
                        $finalPrice = array();
                        $price = array();
                    }

                    foreach($currencies as $code => $currency)
                    {
                        if ($product->getTypeId()=='bundle')
                        {
                            if (!isset($finalPrice[$code]) || !isset($price[$code]))
                            {
                                $_priceModel  = $product->getPriceModel();
                                list($_minimalPriceInclTax, $_maximalPriceInclTax) = $_priceModel->getTotalPrices($product, null, true, false);
                                $finalPrice[$code] = $store->getBaseCurrency()->convert($_minimalPriceInclTax, $currency);
                                $price[$code] = $store->getBaseCurrency()->convert($_minimalPriceInclTax, $currency);
                            }
                        }
                        else
                        {
                            if (!isset($finalPrice[$code]))
                                $finalPrice[$code] = $store->getBaseCurrency()->convert($product->getFinalPrice(), $currency);
                            if (!isset($price[$code]))
                                $price[$code] = $store->getBaseCurrency()->convert($product->getPrice(), $currency);
                        }
                    }

                    $additionalAttributes = array();
                    $eavConfig = Mage::getSingleton('eav/config');
                    foreach ($helper->getProductUpdateAttributes() as $row)
                    {
                        $attribute = $eavConfig->getAttribute('catalog_product', $row['magento_attribute']);
                        if ($attribute && $attribute->getId())
                        {
                            $_attributeText = $product->getAttributeText($attribute->getAttributeCode());
                            $additionalAttributes[$row['recommend_attribute']] = empty($_attributeText)?$product->getData($attribute->getAttributeCode()):$product->getAttributeText($attribute->getAttributeCode());
                            if (is_array($additionalAttributes[$row['recommend_attribute']]))
                            {
                                $additionalAttributes[$row['recommend_attribute']] = implode(', ',$additionalAttributes[$row['recommend_attribute']]);
                            }
                        }
                    }

                    $products[$accountId][$product->getSku()] = array('sku'=>$product->getSku(),'status'=>'online','url'=>$product->getProductUrl(),'price'=>$finalPrice,'original_price'=>$price,'additional_attributes' => $additionalAttributes);
                }
            }
        }
        Mage::app()->setCurrentStore('admin');
        Mage::unregister('_resource_singleton/catalog/product_flat');
        foreach($products as $accountId => $productsData)
        {
            $apiHelper->uploadProductsData(array_values($productsData),$storesByAccounts[$accountId][0]);
        }
    }

    public function updateProductsStatus(Mage_Cron_Model_Schedule $schedule)
    {
        $helper = Mage::helper('superbrecommend');
        if (!$helper->isEnabled()) {
            return $this;
        }
        $helper = Mage::helper('superbrecommend');
        if (!$helper->isStatusCronEnabled()) {
            return $this;
        }
        $apiHelper = Mage::helper('superbrecommend/api');
        $products = array();
        Mage::app()->setCurrentStore('admin');
        $storesByAccounts = array();
        foreach (Mage::app()->getStores() as $store)
        {
            if (!$helper->isEnabled($store->getId()))
                continue;
            $accountId = Mage::getStoreConfig(Superb_Recommend_Helper_Api::XML_PATH_TRACKING_ACCOUNT_ID,$store->getId());
            if (!isset($storesByAccounts[$accountId]))
                $storesByAccounts[$accountId] = array();
            $storesByAccounts[$accountId][] = $store->getId();
        }
        foreach (Mage::app()->getStores() as $store)
        {
            if (!$helper->isEnabled($store->getId()))
                continue;
            $accountId = Mage::getStoreConfig(Superb_Recommend_Helper_Api::XML_PATH_TRACKING_ACCOUNT_ID,$store->getId());
            Mage::app()->setCurrentStore($store);
            Mage::unregister('_resource_singleton/catalog/product_flat');
            $collection = Mage::getResourceModel('catalog/product_collection')->setStore($store)->setStoreId($store->getStoreId());
            $collection
                ->addAttributeToSelect('sku')
                ->addMinimalPrice()
                ->addFinalPrice()
                ->addTaxPercents()
                ->addStoreFilter($store);

            Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($collection);
            Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($collection);
            if (!$apiHelper->getShowOutOfStockProduct($store->getStoreId())) {
                Mage::getSingleton('cataloginventory/stock_status')->addIsInStockFilterToCollection($collection);
            }

            $isEmpty = false;
            $offset = 0;
            while (!$isEmpty) {
                $productCol = clone $collection;
                $productCol->getSelect()->limit(self::LIMIT_STEP, $offset);

                $isEmpty = true;
                foreach($productCol as $product)
                {
                    $isEmpty = false;
                    $offset++;

                    if (!isset($products[$accountId]))
                        $products[$accountId] = array();
                    $products[$accountId][$product->getSku()] = array('sku'=>$product->getSku(),'status'=>'online');
                }
            }
        }
        Mage::app()->setCurrentStore('admin');
        Mage::unregister('_resource_singleton/catalog/product_flat');
        foreach($products as $accountId => $productsData)
        {
            $apiHelper->uploadProductsData(array_values($productsData),$storesByAccounts[$accountId][0]);
        }
    }

    protected function _sortData($a, $b)
    {
        if (isset($a['position']))
            $aPosition = (int)$a['position'];
        else
            $aPosition = 0;
        if (isset($b['position']))
            $bPosition = (int)$b['position'];
        else
            $bPosition = 0;
        if ($aPosition == $bPosition) {
            return 0;
        }
        return ($aPosition < $bPosition) ? -1 : 1;
    }

    public function initSystemConfig(Varien_Event_Observer $observer)
    {
        $storeId = Mage::helper('superbrecommend/admin')->getSystemConfigStoreId();

        $config = $observer->getEvent()->getConfig();

        $slotsPageTypesData = Mage::helper('superbrecommend/api')->getSlotsPageTypesData($storeId);
        uasort($slotsPageTypesData, array($this,'_sortData'));
        $slotsData = Mage::helper('superbrecommend/admin')->getSlotsData($storeId);
        uasort($slotsData, array($this,'_sortData'));
        $slotsDataByPageTypeId = array();
        foreach($slotsData as $slotData)
        {
            if (!isset($slotsDataByPageTypeId[$slotData['page_type_id']]))
                $slotsDataByPageTypeId[$slotData['page_type_id']] = array();
            $slotsDataByPageTypeId[$slotData['page_type_id']][] = $slotData;
        }
        unset($slotsData);

        $i = 0;
        if (is_array($slotsPageTypesData))
        {
            foreach($slotsPageTypesData as $slotsPageTypeData)
            {
                if (isset($slotsDataByPageTypeId[$slotsPageTypeData['id']]))
                {
                    $first = true;
                    foreach($slotsDataByPageTypeId[$slotsPageTypeData['id']] as $slotData)
                    {
                        $slotsByPageTypesXml = '
                            <page_type_'.$slotsPageTypeData['id'].'_'.$slotData['id'].' translate="label comment">
                                <label>'.($first?$slotsPageTypeData['title']:'').'</label>
                                <frontend_type>select</frontend_type>
                                <source_model>superbrecommend/system_config_source_panel</source_model>
                                <backend_model>superbrecommend/system_config_backend_slot</backend_model>
                                <sort_order>'.++$i.'</sort_order>
                                <show_in_default>'.((int)Mage::helper('superbrecommend/admin')->isSingleMode()).'</show_in_default>
                                <show_in_website>'.((int)!Mage::helper('superbrecommend/admin')->isStoreMode()).'</show_in_website>
                                <show_in_store>1</show_in_store>
                            </page_type_'.$slotsPageTypeData['id'].'_'.$slotData['id'].'>
                        ';
                        $config->getNode('sections/superbrecommend/groups/panels/fields')->appendChild(new Mage_Core_Model_Config_Element($slotsByPageTypesXml));
                        $first = false;
                    }
                }
            }
        }
        return $this;
    }

    public function saveSystemConfig(Varien_Event_Observer $observer)
    {
        $storeId = Mage::helper('superbrecommend/admin')->getSystemConfigStoreId();

        $config = $observer->getEvent()->getConfig();

        $panelsBySlotsData = Mage::helper('superbrecommend/admin')->getSaveSlotSystemConfig();
        $slotsData = array();
        foreach($panelsBySlotsData as $slotId=>$panelId)
        {
            $slotsData[] = array('id'=>$slotId,'panel_id'=>$panelId);
        }
        Mage::helper('superbrecommend/api')->updateSlots($slotsData,$storeId);
        
        Mage::helper('superbrecommend/api')->updateAccount($storeId);
        
        return $this;
    }
}
