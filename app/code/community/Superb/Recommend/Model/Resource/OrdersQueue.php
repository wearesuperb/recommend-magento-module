<?php

class Superb_Recommend_Model_Resource_OrdersQueue extends Mage_Core_Model_Resource_Db_Abstract
{
    public function _construct()
    {
        $this->_init('superbrecommend/ordersQueue', 'id');
    }
}