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

class Superb_Recommend_Model_Pagecache_Container_Block extends Enterprise_PageCache_Model_Container_Abstract
{
    /**
     * Get identifier from cookies
     *
     * @return string
     */
    protected function _getIdentifier()
    {
        $cacheId = $this->_getCookieValue(Enterprise_PageCache_Model_Cookie::COOKIE_CUSTOMER, '')
            .(defined('Enterprise_PageCache_Model_Cookie::COOKIE_CUSTOMER_LOGGED_IN') ?
            '_'.$this->_getCookieValue(Enterprise_PageCache_Model_Cookie::COOKIE_CUSTOMER_LOGGED_IN, '')
            :'');
        return $cacheId;
    }

    protected function _isNewTrackingRecived()
    {
        return ($this->_getCookieValue(Superb_Recommend_Helper_Data::COOKIE_RECOMMENDTRACKER) ? true : false);
    }

    /**
     * Get cache identifier
     *
     * @return string
     */
    protected function _getCacheId()
    {
        return $this->_isNewTrackingRecived()?false:'CONTAINER_RECOMMENDTRACKER_' . md5($this->_placeholder->getAttribute('cache_id') . $this->_getIdentifier());
    }

    public function applyWithoutApp(&$content)
    {
        if ($this->_isNewTrackingRecived()) {
            return false;
        }
        return parent::applyWithoutApp($content);
    }

    /**
     * Render block content
     *
     * @return string
     */
    protected function _renderBlock()
    {
        Mage::getSingleton('core/cookie')->delete(Superb_Recommend_Helper_Data::COOKIE_RECOMMENDTRACKER);

        $block = $this->_placeholder->getAttribute('block');
        $block = new $block;

        return $block->toHtml();
    }
}