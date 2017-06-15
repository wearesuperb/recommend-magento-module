<?php
class Superb_Recommend_CartController extends Mage_Core_Controller_Front_Action
{
    public function rebuildAction()
    {
        $rebuildHelper = Mage::helper('superbrecommend/rebuild');
        $data = null;
        $messageId = $this->getRequest()->getParam($rebuildHelper::RECOMMEND_TRACKING_MESSAGE_CODE,false);
        if ($data = $this->getRequest()->getParam('data',false)) {
            $data = unserialize($rebuildHelper->base64UrlDecode($data));
        } elseif (strlen($messageId)) {
            $apiHelper = Mage::helper('superbrecommend/api');
            if (is_string($data = $apiHelper->getCartRebuildData($messageId))) {
                $data = unserialize($rebuildHelper->base64UrlDecode($data));
            }
        }
        if (is_array($data)) {
            $rebuildHelper->rebuildCart($data);
        }
        $cartUrl = Mage::helper('checkout/cart')->getCartUrl();
        $cartUrlDelimiter = (strpos($cartUrl,'?')!==false?'&':'?');
        $queryParams = $cartUrlDelimiter.http_build_query($this->getRequest()->getParams());

        return $this->_redirectUrl($cartUrl.$queryParams);
    }
}
