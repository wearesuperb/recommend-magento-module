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

class Superb_Recommend_Model_System_Config_Source_Panel extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{

    protected $_options;

    public function toOptionArray(){
        if (is_null($this->_options)){
            $storeId = Mage::helper('superbrecommend/admin')->getSystemConfigStoreId();
            $panelsData = Mage::helper('superbrecommend/admin')->getPanelsListData($storeId);
            if (is_array($panelsData))
            {
                $this->_options[] = array('value'=>'','label'=>'');
                foreach($panelsData as $panelData)
                {
                    $this->_options[] = array('value'=>$panelData['id'],'label'=>$panelData['title']);
                }
            }
        }
        return $this->_options;
    }

    public function getAllOptions(){
        return $this->toOptionArray();
    }
}
