<?php

class Superb_Recommend_Model_OrdersQueue extends Mage_Core_Model_Abstract
{
    public function _construct()
    {
        parent::_construct();
        $this->_init('superbrecommend/ordersQueue');
    }
}