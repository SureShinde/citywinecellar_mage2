<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Model;

use Magento\Framework\Model\AbstractModel;
use Amasty\Rewards\Api\Data\RewardsInterface;
use Amasty\Rewards\Helper\Data;

class Rewards extends AbstractModel implements RewardsInterface
{
    /**
     * @var Data
     */
    private $helper;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        Data $helper,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        array $data = []
    ) {
        $this->helper = $helper;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Rewards::class);
    }

    /**
     * @deprecated should save action code. And get name by code only where it is needed.
     * @param string $action
     *
     * @return string
     */
    private function getActionName($action)
    {
        $actionList = $this->helper->getActions();

        if (array_key_exists($action, $actionList)) {
            $result = $actionList[$action];
        } else {
            $result = $action;
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function getActionDate()
    {
        return $this->_getData(RewardsInterface::ACTION_DATE);
    }

    /**
     * @inheritdoc
     */
    public function setActionDate($actionDate)
    {
        $this->setData(RewardsInterface::ACTION_DATE, $actionDate);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAmount()
    {
        return $this->_getData(RewardsInterface::AMOUNT);
    }

    /**
     * @inheritdoc
     */
    public function setAmount($amount)
    {
        $this->setData(RewardsInterface::AMOUNT, $amount);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getComment()
    {
        return $this->_getData(RewardsInterface::COMMENT);
    }

    /**
     * @inheritdoc
     */
    public function setComment($comment)
    {
        $this->setData(RewardsInterface::COMMENT, $comment);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getAction()
    {
        return $this->getActionName($this->_getData(RewardsInterface::ACTION));
    }

    /**
     * @inheritdoc
     */
    public function setAction($action)
    {
        $this->setData(RewardsInterface::ACTION, $action);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getPointsLeft()
    {
        return $this->_getData(RewardsInterface::POINTS_LEFT);
    }

    /**
     * @inheritdoc
     */
    public function setPointsLeft($pointsLeft)
    {
        $this->setData(RewardsInterface::POINTS_LEFT, $pointsLeft);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getCustomerId()
    {
        return $this->_getData(RewardsInterface::CUSTOMER_ID);
    }

    /**
     * @inheritdoc
     */
    public function setCustomerId($customerId)
    {
        $this->setData(RewardsInterface::CUSTOMER_ID, $customerId);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getExpirationId()
    {
        return $this->_getData(RewardsInterface::EXPIRATION_ID);
    }

    /**
     * @inheritdoc
     */
    public function setExpirationId($expirationId)
    {
        $this->setData(RewardsInterface::EXPIRATION_ID, $expirationId);

        return $this;
    }
}
