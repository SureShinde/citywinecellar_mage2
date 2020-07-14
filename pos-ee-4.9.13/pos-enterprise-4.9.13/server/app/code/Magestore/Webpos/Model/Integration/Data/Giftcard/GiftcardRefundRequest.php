<?php

namespace Magestore\Webpos\Model\Integration\Data\Giftcard;

class GiftcardRefundRequest extends \Magento\Framework\DataObject implements \Magestore\Webpos\Api\Data\Integration\Giftcard\GiftcardRefundRequestInterface {
    /**
     * @inheritdoc
     */
    public function getOrderItemId(){
        return $this->getData(self::ORDER_ITEM_ID);
    }

    /**
     * @inheritdoc
     */
    public function setOrderItemId($orderItemId){
        return $this->setData(self::ORDER_ITEM_ID, $orderItemId);
    }

    /**
     * @inheritdoc
     */
    public function getQty(){
        return $this->getData(self::QTY);
    }

    /**
     * @inheritdoc
     */
    public function setQty($qty){
        return $this->setData(self::QTY, $qty);
    }
}