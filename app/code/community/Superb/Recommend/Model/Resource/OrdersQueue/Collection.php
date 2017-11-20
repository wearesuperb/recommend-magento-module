<?php

class Superb_Recommend_Model_Resource_OrdersQueue_Collection extends Mage_Core_Model_Resource_Db_Collection_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('superbrecommend/ordersQueue');
    }
}