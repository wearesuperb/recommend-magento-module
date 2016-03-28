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

class Superb_Recommend_Block_Block extends Mage_Core_Block_Template
{
    protected function _construct()
    {
        $this->setTemplate('recommend_tracker/block.phtml');
    }
    
    public function getTrackingData()
    {
        $data = $this->helper('superbrecommend')->getTrackingData();
        //Mage::log(Mage::helper('core/url')->getCurrentUrl(),null,'superbrecommend.log');
        //Mage::log($data,null,'superbrecommend.log');
        if (Mage::getSingleton('customer/session')->isLoggedIn())
        {
            $data = (is_array($data)?$data:array());
            array_unshift($data,array("setCustomerId",Mage::getSingleton('customer/session')->getCustomerId()));
        }
        return $data;
    }
    
    public function setSuccessPage()
    {
        $data = $this->helper('superbrecommend')->processOrderSuccess();
        //Mage::log($data,null,'superbrecommend.log');
        $this->helper('superbrecommend')->setTrackingData($data);
        
        $data = $this->helper('superbrecommend')->getCartStatusData(Mage::getSingleton('checkout/cart'));
        //Mage::log($data,null,'superbrecommend.log');
        $this->helper('superbrecommend')->setTrackingData($data);
    }
    
    public function setCheckoutPage()
    {
        $data = $this->helper('superbrecommend')->processCheckoutPage();
        //Mage::log($data,null,'superbrecommend.log');
        $this->helper('superbrecommend')->setTrackingData($data);
    }

    public function checkLayerPage()
    {
        if (Mage::getSingleton('catalog/layer')!==false)
        {
            if (count(Mage::getSingleton('catalog/layer')->getState()->getFilters()))
                $this->helper('superbrecommend')->setTrackingData(array('disableRecommendationPanels'),true);
        }
    }

    public function getCachedHtml()
    {
        echo '';
    }

    /**
     * Retrieve cookie value
     *
     * @param string $cookieName
     * @param mixed $defaultValue
     * @return string
     */
    protected function _getCookieValue($cookieName, $defaultValue = null)
    {
        return (array_key_exists($cookieName, $_COOKIE) ? $_COOKIE[$cookieName] : $defaultValue);
    }

    /**
     * Check whether the block can be displayed
     *
     * @return bool
     */
    public function canDisplay()
    {
        return Mage::helper('superbrecommend')->isEnabled();
    }

    /**
     * Output content, if allowed
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->canDisplay()) {
            return '';
        }
        return parent::_toHtml();
    }
}