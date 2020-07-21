<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2020 Amasty (https://www.amasty.com)
 * @package Amasty_Rewards
 */


namespace Amasty\Rewards\Api;

/**
 * @api
 */
interface ExpirationDateRepositoryInterface
{
    /**
     * Save
     *
     * @param \Amasty\Rewards\Api\Data\ExpirationDateInterface $expirationDate
     *
     * @return \Amasty\Rewards\Api\Data\ExpirationDateInterface
     */
    public function save(\Amasty\Rewards\Api\Data\ExpirationDateInterface $expirationDate);

    /**
     * Get by id
     *
     * @param int $id
     *
     * @return \Amasty\Rewards\Api\Data\ExpirationDateInterface
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getById($id);

    /**
     * Get by id
     *
     * @param int $customerId
     *
     * @param bool $isGrouped
     *
     * @return array
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getByCustomerId($customerId, $isGrouped = true);

    /**
     * Get by id
     *
     * @param int $customerId
     *
     * @return \Amasty\Rewards\Api\Data\ExpirationDateInterface[]
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getItemsByCustomerId($customerId);

    /**
     * Delete
     *
     * @param \Amasty\Rewards\Api\Data\ExpirationDateInterface $expirationDate
     *
     * @return bool true on success
     *
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function delete(\Amasty\Rewards\Api\Data\ExpirationDateInterface $expirationDate);

    /**
     * Delete by id
     *
     * @param int $id
     *
     * @return bool true on success
     *
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     */
    public function deleteById($id);

    /**
     * Lists
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     *
     * @return \Magento\Framework\Api\SearchResultsInterface
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Return first rewards model, which have no expiration date by customer id.
     * This model should be one per customer.
     *
     * @param int $customerId
     *
     * @return \Amasty\Rewards\Api\Data\ExpirationDateInterface
     */
    public function getCustomerNonExpireRewards($customerId);
}
