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

class Superb_Recommend_Block_Slot extends Mage_Core_Block_Template
{
    const CALLBACK_FUNCTION_NAME = '__%|CALLBACK_FUNCTION_NAME|%__';
    protected function _construct()
    {
        $this->setTemplate('recommend_tracker/slot.phtml');
    }

    public function getOptions()
    {
        $options = array();
        if ($this->getSlotPosition()!==false)
            $options['slotPosition'] = $this->getSlotPosition();
        if ($this->getPageType()!==false)
            $options['pageType'] = $this->getPageType();
        if ($this->getPageTypePosition()!==false)
            $options['pageTypePosition'] = $this->getPageTypePosition();
        if ($this->getDefaultPanelId()!==false)
            $options['defaultPanelId'] = $this->getDefaultPanelId();
        if (!is_null($this->getCallback()))
            $options['callback'] = self::CALLBACK_FUNCTION_NAME.$this->getCallback().self::CALLBACK_FUNCTION_NAME;
        return $options;
    }
    
    public function getJsonOptions()
    {
        return str_replace(
            array(
                '"'.self::CALLBACK_FUNCTION_NAME,
                self::CALLBACK_FUNCTION_NAME.'"'
            ),
            '',
            json_encode($this->getOptions())
        );
    }
}
