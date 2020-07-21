<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Model;

use Amasty\Rewards\Api\Data\ExpirationDateInterface;
use Magento\Framework\Model\AbstractModel;

class Expiration extends AbstractModel implements ExpirationDateInterface
{
    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Amasty\Rewards\Model\ResourceModel\Expiration::class);
    }

    /**
     * @inheritdoc
     */
    public function getEntityId()
    {
        return $this->_getData(ExpirationDateInterface::ENTITY_ID);
    }

    /**
     * @inheritdoc
     */
    public function setEntityId($entityId)
    {
        $this->setData(ExpirationDateInterface::ENTITY_ID, $entityId);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCustomerId()
    {
        return $this->_getData(ExpirationDateInterface::CUSTOMER_ID);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerId($customerId)
    {
        $this->setData(ExpirationDateInterface::CUSTOMER_ID, $customerId);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDate()
    {
        return $this->_getData(ExpirationDateInterface::DATE);
    }

    /**
     * @inheritdoc
     */
    public function setDate($date)
    {
        $this->setData(ExpirationDateInterface::DATE, $date);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAmount()
    {
        return $this->_getData(ExpirationDateInterface::AMOUNT);
    }

    /**
     * @inheritdoc
     */
    public function setAmount($amount)
    {
        $this->setData(ExpirationDateInterface::AMOUNT, $amount);

        return $this;
    }
}
