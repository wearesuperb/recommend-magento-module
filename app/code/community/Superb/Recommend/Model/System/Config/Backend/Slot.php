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

class Superb_Recommend_Model_System_Config_Backend_Slot extends Mage_Core_Model_Config_Data
{
    protected function _afterLoad()
    {
        $storeId = Mage::helper('superbrecommend/admin')->getSystemConfigStoreId();
        list($pageTypeId,$slotId) = explode('_',str_replace('superbrecommend/panels/page_type_','',$this->getData('path')));
        $slotsData = Mage::helper('superbrecommend/admin')->getSlotsData($storeId);
        if (is_array($slotsData))
        {
            $value = array();
            foreach($slotsData as $slotData)
            {
                if ($slotData['id']==$slotId)
                {
                    $value = $slotData['panel_id'];
                }
            }
            $this->setValue($value);
        }
    }

    protected function _beforeSave()
    {
        list($pageTypeId,$slotId) = explode('_',str_replace('superbrecommend/panels/page_type_','',$this->getData('path')));
        $value = $this->getValue();
        Mage::helper('superbrecommend/admin')->setSaveSlotSystemConfig($slotId,$value);
        $this->setValue(null);
    }
}
