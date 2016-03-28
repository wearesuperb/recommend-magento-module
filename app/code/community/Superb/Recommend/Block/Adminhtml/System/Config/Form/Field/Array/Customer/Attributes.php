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

class Superb_Recommend_Block_Adminhtml_System_Config_Form_Field_Array_Customer_Attributes extends Mage_Adminhtml_Block_System_Config_Form_Field_Array_Abstract
{

    public function __construct(){
        $this->addColumn('recommend_attribute', array(
            'label'     => Mage::helper('superbrecommend')->__('Recommend attribute').'<em class="required">*</em>',
            'style'     => 'width:150px',
            'class'     => 'input-text required-entry',
            'renderer'  => $this->_getRecommendAttributeRenderer()
        ));
        $this->addColumn('magento_attribute', array(
            'label'     => Mage::helper('superbrecommend')->__('Magento attribute').'<em class="required">*</em>',
            'style'     => 'width:150px',
            'class'     => 'input-text required-entry',
            'renderer'  => $this->_getMagentoAttributeRenderer()
        ));
        $this->_addAfter = false;
        $this->_addButtonLabel = Mage::helper('superbrecommend')->__('Add attribute');

        parent::__construct();
    }

    protected function _getRecommendAttributeRenderer(){
        $attribute = Mage::app()->getLayout()->createBlock('superbrecommend/adminhtml_form_field_customer_recommend_attribute');
        $attribute->setIsRenderToJsTemplate(true);
        return $attribute;

    }

    protected function _getMagentoAttributeRenderer(){
        $attribute = Mage::app()->getLayout()->createBlock('superbrecommend/adminhtml_form_field_customer_attribute');
        $attribute->setIsRenderToJsTemplate(true);
        return $attribute;

    }

    public function getArrayRows(){
        $rows = parent::getArrayRows();
        foreach ($rows as $row){
            foreach ($row->getData() as $key => $value){
                if (!isset($this->_columns[$key]))
                    continue;

                $column = $this->_columns[$key];

                // Add hashed template tag with selected value
                if ($column['renderer'] instanceof Mage_Core_Block_Html_Select){
                    $row->setData(sprintf('option_extra_attr_%s',  $column['renderer']->calcOptionHash($value)), 'selected="selected"');
                }
            }
        }
        return $rows;
    }
}
