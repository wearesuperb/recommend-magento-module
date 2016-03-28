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

class Superb_Recommend_Model_System_Config_Backend_Date extends Mage_Core_Model_Config_Data
{
    protected function _beforeSave()
    {
        $value = (string)$this->getValue();
        try {
            $value = $this->formatDate($value);
        } catch (Exception $e) {
            throw Mage::exception('Mage_Eav', Mage::helper('eav')->__('Invalid date'));
        }

        $this->setValue($value);
    }
    public function formatDate($date)
    {
        if (empty($date)) {
            return null;
        }
        // unix timestamp given - simply instantiate date object
        if (preg_match('/^[0-9]+$/', $date)) {
            $date = new Zend_Date((int)$date);
        }
        // international format
        else if (preg_match('#^\d{4}-\d{2}-\d{2}( \d{2}:\d{2}:\d{2})?$#', $date)) {
            $zendDate = new Zend_Date();
            $date = $zendDate->setIso($date);
        }
        // parse this date in current locale, do not apply GMT offset
        else {
            $date = Mage::app()->getLocale()->date($date,
               Mage::app()->getLocale()->getDateFormat(Mage_Core_Model_Locale::FORMAT_TYPE_SHORT),
               null, false
            );
        }
        return $date->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
    }
}
