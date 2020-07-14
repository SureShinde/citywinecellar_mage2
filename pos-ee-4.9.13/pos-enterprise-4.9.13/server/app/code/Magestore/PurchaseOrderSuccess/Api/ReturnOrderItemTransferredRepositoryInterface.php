<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Api;

/**
 * @api
 */
interface ReturnOrderItemTransferredRepositoryInterface
{
    /**
     * Get list return order item transferred that match specified search criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderItemTransferredSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Get a return order item transferred by id
     *
     * @param int $id return order transferred id
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderItemTransferredInterface
     * @throws \Magento\Framework\Exception\NotFoundException
     */
    public function get($id);

    /**
     * Create return order item transferred
     *
     * @param \Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderItemTransferredInterface $returnOrderItemTransferred
     * @return \Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderItemTransferredInterface
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     */
    public function save(\Magestore\PurchaseOrderSuccess\Api\Data\ReturnOrderItemTransferredInterface $returnOrderItemTransferred);
}