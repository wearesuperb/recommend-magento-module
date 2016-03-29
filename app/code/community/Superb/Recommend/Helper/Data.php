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

class Superb_Recommend_Helper_Data extends Mage_Core_Helper_Data
{
    const COOKIE_RECOMMENDTRACKER = 'RECOMMENDTRACKER';

    const XML_PATH_ENABLED                      = 'superbrecommend/general_settings/enabled';
    const XML_PATH_TRACKING_ACCOUNT_ID          = 'superbrecommend/general_settings/account_id';
    const XML_PATH_TRACKING_URL                 = 'superbrecommend/general_settings/server_url';
    const XML_PATH_TRACKING_URL_SECURE          = 'superbrecommend/general_settings/server_secure_url';
    const XML_PATH_DASHBOARD_ENABLED            = 'superbrecommend/general_settings/dashboard';
    const XML_PATH_TRACKING_PRODUCT_ATTRIBUTES  = 'superbrecommend/general_settings/product_attributes';
    const XML_PATH_TRACKING_CUSTOMER_ATTRIBUTES = 'superbrecommend/general_settings/customer_attributes';
    const XML_PATH_ADVANCED                     = 'superbrecommend/general_settings/advanced';
    const XML_PATH_TRACKING_MEDIA_THUMB_SOURCE  = 'superbrecommend/panels/media_thumb_source';
    const XML_PATH_TRACKING_MEDIA_THUMB_WIDTH   = 'superbrecommend/panels/media_thumb_width';
    const XML_PATH_TRACKING_MEDIA_THUMB_HEIGHT  = 'superbrecommend/panels/media_thumb_height';
    const XML_PATH_DATA_CRON_ENABLED            = 'superbrecommend/data_cron/enabled';
    const XML_PATH_STATUS_CRON_ENABLED          = 'superbrecommend/status_cron/enabled';

    static protected $_staticData;

    protected $_childProductLoaded;

    protected function _getSession()
    {
        return Mage::getModel('superbrecommend/session');
    }

    /**
     * Retrieve cookie object
     *
     * @return Mage_Core_Model_Cookie
     */
    public function _getCookie()
    {
        return Mage::getSingleton('core/cookie');
    }

    public function getIsAdvancedModeEnabled()
    {
        return (bool)Mage::getStoreConfig(self::XML_PATH_ADVANCED);
    }

    public function getThumbSource()
    {
        return Mage::getStoreConfig(self::XML_PATH_TRACKING_MEDIA_THUMB_SOURCE);
    }

    public function getThumbWidth()
    {
        $width = Mage::getStoreConfig(self::XML_PATH_TRACKING_MEDIA_THUMB_WIDTH);
        return empty($width)?null:$width;
    }

    public function getThumbHeight()
    {
        $height = Mage::getStoreConfig(self::XML_PATH_TRACKING_MEDIA_THUMB_HEIGHT);
        return empty($height)?null:$height;
    }

    protected function _generateTrackingData($data)
    {
        return $data;
    }

    public function normalizeName($name)
    {
        return trim(preg_replace('/\s+/', ' ', $name));
    }

    public function isEnabled($storeId=null)
    {
        return Mage::getStoreConfig( self::XML_PATH_ENABLED, $storeId );
    }

    public function isDashboardEnabled()
    {
        return Mage::getStoreConfig( self::XML_PATH_DASHBOARD_ENABLED );
    }

    public function isDataCronEnabled($storeId=null)
    {
        return Mage::getStoreConfig( self::XML_PATH_DATA_CRON_ENABLED, $storeId );
    }

    public function isStatusCronEnabled($storeId=null)
    {
        return Mage::getStoreConfig( self::XML_PATH_STATUS_CRON_ENABLED, $storeId );
    }

    public function getAccountId()
    {
        return Mage::getStoreConfig(self::XML_PATH_TRACKING_ACCOUNT_ID);
    }

    public function getApiUrl()
    {
        if (Mage::app()->getStore()->isCurrentlySecure())
            return Mage::getStoreConfig(self::XML_PATH_TRACKING_URL_SECURE);
        else
            return Mage::getStoreConfig(self::XML_PATH_TRACKING_URL);
    }

    public function getApiJsUrl()
    {
        if (Mage::app()->getStore()->isCurrentlySecure())
            return Mage::getStoreConfig(self::XML_PATH_TRACKING_URL_SECURE).'trackerv12.js';
        else
            return Mage::getStoreConfig(self::XML_PATH_TRACKING_URL).'trackerv12.js';
    }

    public function getCustomerRegistrationConfirmData($customer=null)
    {
        if (is_null($customer))
            $customer = Mage::helper('customer')->getCustomer();
        $subscription = Mage::getModel('newsletter/subscriber')->loadByCustomer($customer);
        $data = array(
            'type'              => 'customer-registration',
            'title'             => $customer->getPrefix(),
            'firstname'         => $customer->getFirstname(),
            'lastname'          => $customer->getLastname(),
            'email'             => $customer->getEmail(),
            'email_subscribed'  => $subscription->isSubscribed() ? 'yes' : 'no'
        );
        $data = array(
            'setEcommerceData',
            $data
        );
        return  $this->_generateTrackingData($data);
    }

    public function getCustomerUpdateDetailsData($customer=null)
    {
        if (is_null($customer))
            $customer = Mage::helper('customer')->getCustomer();
        $subscription = Mage::getModel('newsletter/subscriber')->loadByCustomer($customer);
        $data = array(
            'type'              => 'customer-update',
            'title'             => $customer->getPrefix(),
            'firstname'         => $customer->getFirstname(),
            'lastname'          => $customer->getLastname(),
            'email'             => $customer->getEmail(),
            'email_subscribed'  => $subscription->isSubscribed() ? 'yes' : 'no',
            'mobile'            => $customer->getPrimaryBillingAddress() && $customer->getPrimaryBillingAddress()->getId() ? $customer->getPrimaryBillingAddress()->getTelephone():'',
        );
        $data = array(
            'setEcommerceData',
            $data
        );
        return  $this->_generateTrackingData($data);
    }

    public function getCustomerLoginData($customer=null)
    {
        if (is_null($customer))
            $customer = Mage::helper('customer')->getCustomer();
        $subscription = Mage::getModel('newsletter/subscriber')->loadByCustomer($customer);
        $data = array(
            'type'              => 'login',
            'email'             => $customer->getEmail(),
            'customerId'        => $customer->getId(),
            'title'             => $customer->getPrefix(),
            'firstname'         => $customer->getFirstname(),
            'lastname'          => $customer->getLastname(),
            'email'             => $customer->getEmail(),
            'email_subscribed'  => $subscription->isSubscribed() ? 'yes' : 'no',
            'mobile'            => $customer->getPrimaryBillingAddress() && $customer->getPrimaryBillingAddress()->getId() ? $customer->getPrimaryBillingAddress()->getTelephone():'',
        );
        $data = array(
            'setEcommerceData',
            $data
        );
        return  $this->_generateTrackingData($data);
    }

    public function getCustomerCustomData($customer=null)
    {
        if (is_null($customer))
            $customer = Mage::helper('customer')->getCustomer();

        $data = array();
        $eavConfig = Mage::getSingleton('eav/config');
        foreach ($this->getCustomerUpdateAttributes() as $row)
        {
            $attribute = $eavConfig->getAttribute('customer', $row['magento_attribute']);
            if ($attribute && $attribute->getId())
            {
                $_attributeText = $customer->getAttributeText($attribute->getAttributeCode());
                $data[] = $this->_generateTrackingData(array(
                    'setCustomerCustomVar',
                    $row['recommend_attribute'],
                    empty($_attributeText)?$customer->getData($attribute->getAttributeCode()):$customer->getAttributeText($attribute->getAttributeCode())
                ));
            }
        }
        return  $data;
    }

    protected function getCategoryPathName($_category)
    {
        if (is_null($_category))
            $_category = Mage::registry('current_category');

        $categoriesPath = array();
        if ($_category) {
            $pathInStore = $_category->getPathInStore();
            $pathIds = array_reverse(explode(',', $pathInStore));

            $categories = $_category->getParentCategories();

            // add category path breadcrumb
            foreach ($pathIds as $categoryId) {
                if (isset($categories[$categoryId]) && $categories[$categoryId]->getName()) {
                    $categoriesPath[] = $this->normalizeName($categories[$categoryId]->getName());
                }
            }
        }
        return implode(DS,$categoriesPath);
    }

    public function getProductUpdateAttributes()
    {
        $attributes = unserialize((string)Mage::getStoreConfig(self::XML_PATH_TRACKING_PRODUCT_ATTRIBUTES));
        return $attributes;
    }

    public function getCustomerUpdateAttributes()
    {
        $attributes = unserialize((string)Mage::getStoreConfig(self::XML_PATH_TRACKING_CUSTOMER_ATTRIBUTES));
        return $attributes;
    }

    public function getProductViewData($_product=null,$_currentCategory=null)
    {
        if (is_null($_currentCategory))
            $_currentCategory = Mage::registry('current_category');

        if (is_null($_product))
            $_product = Mage::registry('current_product');

        $categories = array();
        foreach ($_product->getCategoryCollection() as $_category) {
            $categoryPathName = $this->getCategoryPathName($_category);
            if (!empty($categoryPathName)) $categories[] = $this->normalizeName($categoryPathName);
        }

        $additionalAttributes = array();
        $eavConfig = Mage::getSingleton('eav/config');
        foreach ($this->getProductUpdateAttributes() as $row)
        {
            $attribute = $eavConfig->getAttribute('catalog_product', $row['magento_attribute']);
            if ($attribute && $attribute->getId())
            {
                $_attributeText = $_product->getAttributeText($attribute->getAttributeCode());
                $additionalAttributes[$row['recommend_attribute']] = empty($_attributeText)?$_product->getData($attribute->getAttributeCode()):$_product->getAttributeText($attribute->getAttributeCode());
                if (is_array($additionalAttributes[$row['recommend_attribute']]))
                {
                    $additionalAttributes[$row['recommend_attribute']] = implode(', ',$additionalAttributes[$row['recommend_attribute']]);
                }
            }
        }

        if ($_product->getTypeId()=='bundle')
        {
            $_priceModel  = $_product->getPriceModel();
            list($_minimalPriceInclTax, $_maximalPriceInclTax) = $_priceModel->getTotalPrices($_product, null, true, false);
            $_finalPrice = $_minimalPriceInclTax;
            $_price = $_minimalPriceInclTax;
        }
        else
        {
            $_price = $_product->getPrice();
            $_finalPrice = $_product->getFinalPrice();
        }
        $imageUrl = (string)Mage::helper('catalog/image')->init($_product, $this->getThumbSource())->resize($this->getThumbWidth(), $this->getThumbHeight());
        $secureImageUrl = str_replace(Mage::getBaseUrl('media',false),Mage::getBaseUrl('media',true),$imageUrl);
        $data = array(
            'setEcommerceData',
            array(
                'type'                  => 'product-view',
                'name'                  => $this->normalizeName($_product->getName()),
                'sku'                   => $_product->getSku(),
                'image'                 => $imageUrl,
                'secure_image'          => $secureImageUrl,
                'url'                   => $_product->getUrlModel()->getUrl($_product, array('_ignore_category'=>true)),
                'categories'            => $categories,
                'price'                 => Mage::helper('core')->currency($_finalPrice,false,false),
                'original_price'        => Mage::helper('core')->currency($_price,false,false),
                'additional_attributes' => $additionalAttributes
            )
        );
        if (is_object($_currentCategory))
            $data[1]['current_category'] = $this->normalizeName($this->getCategoryPathName($_currentCategory));
        return  $this->_generateTrackingData($data);
    }

    public function getCategoryViewData($_category=null)
    {
        if (is_null($_category))
            $_category = Mage::registry('current_category');

        $data = array(
            'setEcommerceData',
            array(
                'type'          => 'category-view',
                'name'          => $this->normalizeName($this->getCategoryPathName($_category)),
                'url'           => Mage::helper('core/url')->getCurrentUrl()
            )
        );
        return  $this->_generateTrackingData($data);
    }

    protected function getLoadedChildProduct($item){
        $product = $this->getChildProduct($item);
        if (is_null($this->_childProductLoaded) || $this->_childProductLoaded->getId() != $product->getId()){
            $this->_childProductLoaded = $product->load($product->getId());
        }
        return $this->_childProductLoaded;
    }

    /**
     * Get item configurable child product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getChildProduct($item)
    {
        if ($option = $item->getOptionByCode('simple_product')) {
            return $option->getProduct();
        }
        return null;
    }

    /**
     * Get product thumbnail image
     *
     * @return Mage_Catalog_Model_Product_Image
     */
    protected function getCartItemThumbnail($item)
    {
        $product = $this->getChildProduct($item);

        if (!$product || !$product->getData('thumbnail')
            || ($product->getData('thumbnail') == 'no_selection')
            /*|| (Mage::getStoreConfig(self::CONFIGURABLE_PRODUCT_IMAGE) == self::USE_PARENT_IMAGE)*/){
            $product = $item->getProduct();
        }
        return Mage::helper('catalog/image')->init($product, 'thumbnail');
    }

    public function getCartStatusData($_cart=null)
    {
        if (is_null($_cart))
            $_cart = Mage::getSingleton('checkout/cart');
        $_items = $_cart->getQuote()->getAllVisibleItems();
        $data = array(
            'type'          => 'cart-update',
            'grand-total'   => sprintf('%01.2f',$_cart->getQuote()->getGrandTotal()),
            'total-qty'     => (int)$_cart->getSummaryQty(),
            'products'      => array()
        );
        foreach($_items as $_item)
        {
            $itemData = array();
            $itemData['product-name']  = $this->normalizeName($_item->getProduct()->getName());
            $itemData['product-sku']  = $_item->getProduct()->getData('sku');
            $itemData['product-image'] = (string)$this->getCartItemThumbnail($_item)->resize($this->getThumbWidth(), $this->getThumbHeight());
            $productUrl = '';
            if ($_item->getRedirectUrl())
            {
                $productUrl = $_item->getRedirectUrl();
            }
            else
            {
                $product = $_item->getProduct();
                $option  = $_item->getOptionByCode('product_type');
                if ($option) {
                    $product = $option->getProduct();
                }
                $productUrl = $product->getUrlModel()->getUrl($product);
            }
            $itemData['product-url']  = $productUrl;

            $options = array();
            if ($optionIds = $_item->getOptionByCode('option_ids')) {
                $options = array();
                foreach (explode(',', $optionIds->getValue()) as $optionId) {
                    if ($option = $_item->getProduct()->getOptionById($optionId)) {

                        $quoteItemOption = $_item->getOptionByCode('option_' . $option->getId());

                        $group = $option->groupFactory($option->getType())
                            ->setOption($option)
                            ->setQuoteItemOption($quoteItemOption);

                        $options[] = array(
                            'label' => $option->getTitle(),
                            'value' => $group->getFormattedOptionValue($quoteItemOption->getValue()),
                            'print_value' => $group->getPrintableOptionValue($quoteItemOption->getValue()),
                            'option_id' => $option->getId(),
                            'option_type' => $option->getType(),
                            'custom_view' => $group->isCustomizedView()
                        );
                    }
                }
            }
            if ($addOptions = $_item->getOptionByCode('additional_options')) {
                $options = array_merge($options, unserialize($addOptions->getValue()));
            }

            $attributes = array();
            if ($_item->getProduct()->getTypeId()==Mage_Catalog_Model_Product_Type::TYPE_CONFIGURABLE)
            {
                $attributes = $_item->getProduct()->getTypeInstance(true)
                    ->getSelectedAttributesInfo($_item->getProduct());
            }
            $options = array_merge($attributes, $options);
            $_options = array();
            foreach($options as $_option)
            {
                $_options[] = $_option['value'];
            }
            $itemData['product-attribute']  = implode(',',$_options);
            $itemData['product-qty']  = $_item->getQty()*1;
            $itemData['product-price']  = sprintf('%01.2f',Mage::helper('checkout')->getPriceInclTax($_item));
            $itemData['product-total-val']  = sprintf('%01.2f',Mage::helper('checkout')->getSubtotalInclTax($_item));
            $data['products'][] = $itemData;
        }
        $data = array(
            'setEcommerceData',
            $data
        );
        return  $this->_generateTrackingData($data);
    }

    public function getWishlistUpdatedData()
    {
        $_wishlist = Mage::helper('wishlist')->getWishlist();
        $_items = $_wishlist->getItemCollection();
        $data = array(
            'type'      => 'wishlist-update',
            'products'  => array()
        );
        foreach($_items as $_item)
        {
            $itemData = array();
            $itemData['product-name']  = $this->normalizeName($_item->getProduct()->getName());
            $buyRequest = $_item->getBuyRequest();
            if (is_object($buyRequest)) {
                $config = $buyRequest->getSuperProductConfig();
                if ($config && !empty($config['product_id'])) {
                    $product = Mage::getModel('catalog/product')
                        ->setStoreId(Mage::app()->getStore()->getStoreId())
                        ->load($config['product_id']);
                    $_item->setProduct($product);
                }
            }

            $itemData['product-sku']  = $_item->getProduct()->getSku();
            $itemData['product-image'] = (string)Mage::helper('catalog/image')->init($_item->getProduct(), $this->getThumbSource())->resize($this->getThumbWidth(), $this->getThumbHeight());
            $productUrl = '';
            $productUrl = $_item->getProduct()->getUrlModel()->getUrl($_item->getProduct());
            $itemData['product-url']  = $productUrl;
            $itemData['product-price']  = sprintf('%01.2f',$_item->getProduct()->getFinalPrice());
            $date = Mage::app()->getLocale()->storeDate('', $_item->getAddedAt(), true);
            $itemData['product-date-Added']  = $date->toString('dd.MM.YYYY');
            $data['products'][] = $itemData;
        }
        $data = array(
            'setEcommerceData',
            $data
        );
        return  $this->_generateTrackingData($data);
    }

    public function getCustomerUnsubscribeData($email)
    {
        $customer = Mage::getModel('customer/customer')->setWebsiteId(Mage::app()->getWebsite()->getId())->loadByEmail($email);
        $data = array(
            'type'      => 'unsubscribe',
            'email'     => $email,
            'customerId'=> $customer && $customer->getId()?$customer->getId():''
        );
        $data = array(
            'setEcommerceData',
            $data
        );
        return  $this->_generateTrackingData($data);
    }

    public function getCustomerSubscribeData($email)
    {
        $customer = Mage::getModel('customer/customer')->setWebsiteId(Mage::app()->getWebsite()->getId())->loadByEmail($email);
        $data = array(
            'type'          => 'subscribe',
            'email'         => $email
        );
        $data = array(
            'setEcommerceData',
            $data
        );
        return  $this->_generateTrackingData($data);
    }

    public function processOrderSuccess()
    {
        $order = Mage::getModel('sales/order')->load(Mage::getSingleton('checkout/session')->getLastOrderId());
        $_items = $order->getItemsCollection();
        $data = array(
            'type'              => 'sale',
            'sale-qty'          => '',
            'email'             => $order->getCustomerEmail(),
            'firstname'         => $order->getCustomerFirstname(),
            'lastname'          => $order->getCustomerLastname(),
            'sale-grand-total'  => $order->getBaseGrandTotal(),
            'sale-tax'          => $order->getBaseTaxAmount(),
            'sale-delivery'     => $order->getBaseShippingAmount(),
            'sale-ref'          => $order->getIncrementId(),
            'sale-currency'     => $order->getBaseCurrencyCode()
        );
        $_qtyOrdered = 0;
        foreach($_items as $_item)
        {
            if (!$_item->getHasChildren() && !$_item->getParentItem())
            {
                $_qtyOrdered += $_item->getQtyOrdered();
                $itemData = array();
                $itemData['sale-product-name'] = $this->normalizeName($_item->getName());
                $itemData['sale-product-sku'] = $_item->getProductOptionByCode('recommend-product-view-sku');
                $itemData['sale-product-qty']  = $_item->getQtyOrdered();
                $itemData['sale-product-val']  = sprintf('%.2f', $_item->getBasePriceInclTax());
                $data['products'][] = $itemData;
            }
            elseif ($_item->getParentItem())
            {
                $_qtyOrdered += $_item->getQtyOrdered();
                $itemData = array();
                $itemData['sale-product-name'] = $this->normalizeName($_item->getName());
                $itemData['sale-product-sku'] = $_item->getParentItem()->getProductOptionByCode('recommend-product-view-sku');
                $itemData['sale-product-qty']  = $_item->getParentItem()->getQtyOrdered();
                $itemData['sale-product-val']  = sprintf('%.2f', $_item->getParentItem()->getBasePriceInclTax());
                $data['products'][] = $itemData;
            }
        }
        $data['sale-qty'] = $_qtyOrdered;
        $data = array(
            'setEcommerceData',
            $data
        );
        return  $this->_generateTrackingData($data);
    }

    public function processCheckoutPage()
    {
        $data = array(
            'type'              => 'checkout-view',
        );
        $data = array(
            'setEcommerceData',
            $data
        );
        return  $this->_generateTrackingData($data);
    }

    public function setTrackingData($record,$static=false)
    {
        if ($static)
            $data = $this->getStaticTrackingData();
        else
            $data = $this->_getSession()->getTrackingData();
        if (is_array($record))
            $data[] = $record;
        else
            $data = array($record);
        if ($static)
            $this->setStaticTrackingData($data);
        else
        {
            $this->_getSession()->setTrackingData($data);
            $this->_getCookie()->set(self::COOKIE_RECOMMENDTRACKER, '1');
        }
    }

    public function getTrackingData($clear=true,$static=false)
    {
        if ($static)
            $data = $this->getStaticTrackingData();
        else
            $data = $this->_getSession()->getTrackingData();
        if ($clear)
        {
            if ($static)
                $this->setStaticTrackingData(array());
            else
                $this->_getSession()->setTrackingData(array());
        }
        return $data;
    }

    public function getStaticTrackingData()
    {
        if (!is_array(self::$_staticData)){
            return array();
        }
        return self::$_staticData;
    }

    public function setStaticTrackingData($data)
    {
        self::$_staticData = $data;
        return $this;
    }
}
