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

class Superb_Recommend_Block_Static_Block extends Mage_Core_Block_Template
{
    protected function _construct()
    {
        $this->setTemplate('recommend_tracker/static.phtml');
    }

    public function getTrackingData()
    {
        $data = $this->helper('superbrecommend')->getTrackingData(true,true);
        //Mage::log(Mage::helper('core/url')->getCurrentUrl(),null,'superbrecommend.log');
        //Mage::log($data,null,'superbrecommend.log');
        return $data;
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