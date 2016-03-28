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

class Superb_Recommend_Block_Adminhtml_Form_Field_Product_Attribute extends Mage_Core_Block_Html_Select
{

    protected function _getAttributes(){
        if (!$this->hasData('attributes')){
            $attributes = Mage::getModel('superbrecommend/system_config_source_product_attribute')->toOptionArray();
            $this->setData('attributes', $attributes);
        }
        return $this->_getData('attributes');
    }

    public function _toHtml(){
        if (!$this->getOptions()){
            foreach($this->_getAttributes() as $attribute){
                $this->addOption($attribute['value'], $this->quoteEscape($attribute['label']));
            }
        }
        return parent::_toHtml();
    }

    public function setInputName($value){
        return $this->setName($value);
    }

    protected function _optionToHtml($option, $selected=false){
        $selectedHtml = $selected ? ' selected="selected"' : '';
        if ($this->getIsRenderToJsTemplate() === true){
            $selectedHtml .= ' #{option_extra_attr_' . self::calcOptionHash($option['value']) . '}';
        }
        $html = '<option value="'.$this->htmlEscape($option['value']).'"'.$selectedHtml.'>'.$this->escapeHtml($this->jsQuoteEscape($option['label'])).'</option>';

        return $html;
    }

    public function calcOptionHash($optionValue){
        return sprintf('%u', crc32($this->getName() . $this->getId() . $optionValue));
    }

}
