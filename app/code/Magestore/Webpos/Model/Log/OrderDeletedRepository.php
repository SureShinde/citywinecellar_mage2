<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Log;
/**
 * Class OrderDeletedRepository
 * @package Magestore\Webpos\Model\Log
 */
class OrderDeletedRepository implements \Magestore\Webpos\Api\Log\OrderDeletedRepositoryInterface
{
    /**
     * @var \Magestore\Webpos\Api\Data\Log\OrderDeletedInterface
     */
    protected $orderDeleted;
    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Log\OrderDeleted
     */
    protected $orderDeletedResource;

    /**
     * OrderDeletedRepository constructor.
     * @param \Magestore\Webpos\Api\Data\Log\OrderDeletedInterface $orderDeleted
     * @param \Magestore\Webpos\Model\ResourceModel\Log\OrderDeleted $orderDeletedResource
     */
    public function __construct(
        \Magestore\Webpos\Api\Data\Log\OrderDeletedInterface $orderDeleted,
        \Magestore\Webpos\Model\ResourceModel\Log\OrderDeleted $orderDeletedResource
    )
    {
        $this->orderDeleted = $orderDeleted;
        $this->orderDeletedResource = $orderDeletedResource;
    }
    /**
     * @param \Magestore\Webpos\Api\Data\Log\OrderDeletedInterface $orderDeleted
     * @return \Magestore\Webpos\Api\Data\Log\OrderDeletedInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Magestore\Webpos\Api\Data\Log\OrderDeletedInterface $orderDeleted){
        try {
            $this->orderDeletedResource->save($orderDeleted);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(__('Unable to save order deleted'));
        }
        return $orderDeleted;
    }

    /**
     * @param \Magestore\Webpos\Api\Data\Log\OrderDeletedInterface $orderDeleted
     * @return boolean
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Magestore\Webpos\Api\Data\Log\OrderDeletedInterface $orderDeleted){
        return $this->deleteById($orderDeleted->getId());
    }

    /**
     * @param int $orderId
     * @return boolean
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($orderId){
        try {
            $orderDeleted = $this->getById($orderId);
            $this->orderDeletedResource->delete($orderDeleted);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotDeleteException(__('Unable to delete order deleted'));
        }
    }
    /**
     * {@inheritdoc}
     */
    public function getById($orderId) {
        $orderDeleted = $this->orderDeleted;
        $this->orderDeletedResource->load($orderDeleted, $orderId);
        if (!$orderDeleted->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('Order with id "%1" does not exist.', $orderId));
        } else {
            return $orderId;
        }
    }
}
