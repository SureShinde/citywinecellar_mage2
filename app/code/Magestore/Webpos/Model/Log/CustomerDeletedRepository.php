<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Log;
/**
 * Class CustomerDeletedRepository
 * @package Magestore\Webpos\Model\Log
 */
class CustomerDeletedRepository implements \Magestore\Webpos\Api\Log\CustomerDeletedRepositoryInterface
{
    /**
     * @var \Magestore\Webpos\Api\Data\Log\CustomerDeletedInterface
     */
    protected $customerDeleted;
    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Log\CustomerDeleted
     */
    protected $customerDeletedResource;

    /**
     * CustomerDeletedRepository constructor.
     * @param \Magestore\Webpos\Api\Data\Log\CustomerDeletedInterface $customerDeleted
     * @param \Magestore\Webpos\Model\ResourceModel\Log\CustomerDeleted $customerDeletedResource
     */
    public function __construct(
        \Magestore\Webpos\Api\Data\Log\CustomerDeletedInterface $customerDeleted,
        \Magestore\Webpos\Model\ResourceModel\Log\CustomerDeleted $customerDeletedResource
    )
    {
        $this->customerDeleted = $customerDeleted;
        $this->customerDeletedResource = $customerDeletedResource;
    }
    /**
     * @param \Magestore\Webpos\Api\Data\Log\CustomerDeletedInterface $customerDeleted
     * @return \Magestore\Webpos\Api\Data\Log\CustomerDeletedInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Magestore\Webpos\Api\Data\Log\CustomerDeletedInterface $customerDeleted){
        try {
            $this->customerDeletedResource->save($customerDeleted);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(__('Unable to save customer deleted'));
        }
        return $customerDeleted;
    }

    /**
     * @param \Magestore\Webpos\Api\Data\Log\CustomerDeletedInterface $customerDeleted
     * @return boolean
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(\Magestore\Webpos\Api\Data\Log\CustomerDeletedInterface $customerDeleted){
        return $this->deleteById($customerDeleted->getId());
    }

    /**
     * @param int $customerId
     * @return boolean
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($customerId){
        try {
            $customerDeleted = $this->getById($customerId);
            $this->customerDeletedResource->delete($customerDeleted);
        } catch (\Exception $e) {
            throw new \Magento\Framework\Exception\CouldNotDeleteException(__('Unable to delete customer deleted'));
        }
    }
    /**
     * {@inheritdoc}
     */
    public function getById($customerId) {
        $customerDeleted = $this->customerDeleted;
        $this->customerDeletedResource->load($customerDeleted, $customerId);
        if (!$customerDeleted->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(__('Customer with id "%1" does not exist.', $customerId));
        } else {
            return $customerId;
        }
    }
}
