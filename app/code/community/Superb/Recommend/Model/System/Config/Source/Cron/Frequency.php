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

class Superb_Recommend_Model_System_Config_Source_Cron_Frequency
{

    protected static $_options;

    const CRON_EVERY_5_MINUTES  = 'every5minutes';
    const CRON_HOURLY           = 'hourly';
    const CRON_EVERY_3_HOURS    = 'every3hours';
    const CRON_DAILY            = 'daily';

    public function toOptionArray()
    {
        if (!self::$_options) {
            self::$_options = array(
                array(
                    'label' => Mage::helper('cron')->__('Every 5 minutes'),
                    'value' => self::CRON_EVERY_5_MINUTES,
                ),
                array(
                    'label' => Mage::helper('cron')->__('Hourly'),
                    'value' => self::CRON_HOURLY,
                ),
                array(
                    'label' => Mage::helper('cron')->__('Every 3 hours'),
                    'value' => self::CRON_EVERY_3_HOURS,
                ),
                array(
                    'label' => Mage::helper('cron')->__('Daily'),
                    'value' => self::CRON_DAILY,
                ),
            );
        }
        return self::$_options;
    }

}
