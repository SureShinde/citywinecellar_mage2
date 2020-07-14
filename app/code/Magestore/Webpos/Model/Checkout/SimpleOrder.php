<?php

namespace Magestore\Webpos\Model\Checkout;

class SimpleOrder extends \Magento\Framework\Model\AbstractModel implements \Magestore\Webpos\Api\Data\Checkout\SimpleOrderInterface
{


    /**
     * @inheritdoc
     */
    public function getEntityId()
    {
        return $this->getData(self::ENTITY_ID);
    }

    /**
     * @inheritdoc
     */
    public function setEntityId($entityId)
    {
        return $this->setData(self::ENTITY_ID, $entityId);
    }

    /**
     * @inheritdoc
     */
    public function getState()
    {
        return $this->getData(self::STATE);
    }

    /**
     * @inheritdoc
     */
    public function setState($state)
    {
        return $this->setData(self::STATE, $state);
    }

    /**
     * @inheritdoc
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS);
    }

    /**
     * @inheritdoc
     */
    public function setStatus($status)
    {
        return $this->setData(self::STATUS, $status);
    }

    /**
     * @inheritdoc
     */
    public function getGrandTotal()
    {
        return $this->getData(self::GRAND_TOTAL);
    }

    /**
     * @inheritdoc
     */
    public function setGrandTotal($grandTotal)
    {
        return $this->setData(self::GRAND_TOTAL, round($grandTotal, 4));
    }

    /**
     * @inheritdoc
     */
    public function getTotalPaid()
    {
        return $this->getData(self::TOTAL_PAID);
    }

    /**
     * @inheritdoc
     */
    public function setTotalPaid($totalPaid)
    {
        return $this->setData(self::TOTAL_PAID, round($totalPaid, 4));
    }

    /**
     * @inheritdoc
     */
    public function getTotalDue()
    {
        return $this->getData(self::TOTAL_DUE);
    }

    /**
     * @inheritdoc
     */
    public function setTotalDue($totalDue)
    {
        return $this->setData(self::TOTAL_DUE, round($totalDue, 4));
    }

    /**
     * @inheritdoc
     */
    public function getIncrementId()
    {
        return $this->getData(self::INCREMENT_ID);
    }

    /**
     * @inheritdoc
     */
    public function setIncrementId($incrementId)
    {
        return $this->setData(self::INCREMENT_ID, $incrementId);
    }

    /**
     * @inheritdoc
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritdoc
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }
    /**
     * Retrieve existing extension attributes object or create a new one.
     *
     * @return \Magestore\Webpos\Api\Data\Checkout\SimpleOrderExtensionInterface|null
     * @since 102.0.0
     */
    public function getExtensionAttributes()
    {
        return $this->getData(self::EXTENSION_ATTRIBUTES_KEY);
    }

    /**
     * Set an extension attributes object.
     *
     * @param \Magestore\Webpos\Api\Data\Checkout\SimpleOrderExtensionInterface $extensionAttributes
     * @return $this
     * @since 102.0.0
     */
    public function setExtensionAttributes(
        \Magestore\Webpos\Api\Data\Checkout\SimpleOrderExtensionInterface $extensionAttributes
    ){
        return $this->setData(self::EXTENSION_ATTRIBUTES_KEY, $extensionAttributes);
    }
}