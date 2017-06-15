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

class Superb_Recommend_Helper_Rebuild extends Mage_Core_Helper_Data
{

    CONST RECOMMEND_TRACKING_MESSAGE_CODE = 'recommend-message';

    public function getDecryptHelper()
    {
        return Mage::helper('core/data');
    }

    /**
     * Base64 url encode
     *
     * @param string $data
     * @return string
     */
    public function base64UrlEncode($data)
    {
        return strtr($this->getDecryptHelper()->encrypt(base64_encode($data)), '+/=', '-_,');
    }

    /**
     * Base64 url dencode
     *
     * @param string $data
     * @return string
     */
    public function base64UrlDecode($data)
    {
        return base64_decode($this->getDecryptHelper()->decrypt(strtr($data, '-_,', '+/=')));
    }

    public function getProduct($buyRequest, $storeId)
    {
        if ($buyRequest instanceof Varien_Object) {
            if (!$buyRequest->getProduct()) {
                return false;
            }

            $product = Mage::getModel('catalog/product')
                ->setStoreId($storeId)
                ->load($buyRequest->getProduct());
            return $product;
        }
        return false;
    }

    public function rebuildCart($data)
    {
        $cart = Mage::getSingleton('checkout/cart');
        if ((int)$cart->getQuote()->getItemsCount())
            return ;
        $cartUpdated = false;
        foreach($data as $buyRequest) {
            try {
                $storeId = Mage::app()->getStore()->getId();
                $product = $this->getProduct($buyRequest, $storeId);

                if ($product->getStatus() != Mage_Catalog_Model_Product_Status::STATUS_ENABLED) {
                    continue;
                }

                if (!$product->isVisibleInSiteVisibility()) {
                    if ($product->getStoreId() == $storeId) {
                        continue;
                    }
                }

                if ($product->isSalable()) {
                    $cart->addProduct($product, $buyRequest);
                    if (!$product->isVisibleInSiteVisibility()) {
                        $cart->getQuote()->getItemByProduct($product)->setStoreId($storeId);
                    }
                    $cartUpdated = true;
                }
            } catch (Exception $e) {
                Mage::logException($e);
                foreach($cart->getItems() as $itemId => $item) {
                    if ($item->getHasError()) {
                        if (is_null($item->getId())) {
                            $cart->getQuote()->getItemsCollection()->removeItemByKey($itemId);
                        } else {
                            $cart->removeItem($item->getId());
                        }
                    }
                }
            }
        }
        if ($cartUpdated) {
            try {
                $cart->save()->getQuote()->collectTotals();
            } catch (Exception $e) {
                Mage::logException($e);
            }
        }
    }
}
