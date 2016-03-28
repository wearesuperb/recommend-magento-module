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

class Superb_Recommend_Model_System_Config_Backend_Status_Cron extends Mage_Core_Model_Config_Data
{
    const CRON_STRING_PATH  = 'crontab/jobs/superbrecommend_update_products_status/schedule/cron_expr';
    const CRON_MODEL_PATH   = 'crontab/jobs/superbrecommend_update_products_status/run/model';

    protected $_cronExprToFrequncy = array(
        Superb_Recommend_Model_System_Config_Source_Cron_Frequency::CRON_EVERY_5_MINUTES=>'*/5 * * * *',
        Superb_Recommend_Model_System_Config_Source_Cron_Frequency::CRON_HOURLY=>'1 * * * *',
        Superb_Recommend_Model_System_Config_Source_Cron_Frequency::CRON_EVERY_3_HOURS=>'1 */3 * * *',
        Superb_Recommend_Model_System_Config_Source_Cron_Frequency::CRON_DAILY=>'1 5 * * *'
    );

    /**
     * Cron settings after save
     *
     */
    protected function _afterSave()
    {
        $frequncy   = $this->getData('groups/status_cron/fields/frequency/value');

        $cronExprString = $this->_cronExprToFrequncy[$frequncy];
        try {
            Mage::getModel('core/config_data')
                ->load(self::CRON_STRING_PATH, 'path')
                ->setValue($cronExprString)
                ->setPath(self::CRON_STRING_PATH)
                ->save();

            Mage::getModel('core/config_data')
                ->load(self::CRON_MODEL_PATH, 'path')
                ->setValue((string) Mage::getConfig()->getNode(self::CRON_MODEL_PATH))
                ->setPath(self::CRON_MODEL_PATH)
                ->save();
        }
        catch (Exception $e) {
            Mage::throwException(Mage::helper('adminhtml')->__('Unable to save the cron expression.'));
        }
    }
}
