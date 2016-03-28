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

class Superb_Recommend_Model_System_Config_Source_Customer_Attribute extends Mage_Eav_Model_Entity_Attribute_Source_Abstract
{

    protected $_options;

    public function toOptionArray(){
        if (is_null($this->_options)){
            $type = Mage::getModel('eav/entity_type')->loadByCode('customer');
            $attributes = Mage::getResourceModel('eav/entity_attribute_collection')
                ->addStoreLabel(0)
                ->setEntityTypeFilter($type);

            $this->_options = array(array(
                'value' => '',
                'label' => '',
            ));
            foreach ($attributes as $attribute){
                if ($attribute->getStoreLabel()){
                    $this->_options[] = array(
                        'value' => $attribute->getAttributeCode(),
                        'label' => $attribute->getStoreLabel()
                    );
                }
            }
            sort($this->_options);
        }
        return $this->_options;
    }

    public function getAllOptions(){
        return $this->toOptionArray();
    }
}



