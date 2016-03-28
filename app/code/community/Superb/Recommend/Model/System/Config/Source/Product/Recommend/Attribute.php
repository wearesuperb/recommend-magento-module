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

class Superb_Recommend_Model_System_Config_Source_Product_Recommend_Attribute extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{
    protected $_options;

    public function toOptionArray(){
        if (is_null($this->_options)){
            $storeId = Mage::helper('superbrecommend/admin')->getSystemConfigStoreId();
            $productAttributesData = Mage::helper('superbrecommend/admin')->getProductAttributesListData($storeId);
            $this->_options[] = array('value'=>'','label'=>'');
            if (is_array($productAttributesData))
            {
                foreach($productAttributesData as $productAttributeData)
                {
                    $this->_options[] = array('value'=>$productAttributeData['code'],'label'=>$productAttributeData['title']);
                }
            }
        }
        return $this->_options;
    }

    public function getAllOptions(){
        return $this->toOptionArray();
    }
}
