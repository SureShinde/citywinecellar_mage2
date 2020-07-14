<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Log;

/**
 * Interface CustomerDeletedRepositoryInterface
 * @package Magestore\Webpos\Api\Log
 */
interface CustomerDeletedRepositoryInterface {

    /**
     * @param \Magestore\Webpos\Api\Data\Log\CustomerDeletedInterface $customerDeleted
     * @return \Magestore\Webpos\Api\Data\Log\CustomerDeletedInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Magestore\Webpos\Api\Data\Log\CustomerDeletedInterface $customerDeleted);
    /**
     * @param \Magestore\Webpos\Api\Data\Log\CustomerDeletedInterface $customerDeleted
     * @return boolean
     */
    public function delete(\Magestore\Webpos\Api\Data\Log\CustomerDeletedInterface $customerDeleted);

    /**
     * @param int $customerId
     * @return boolean
     */
    public function deleteById($customerId);

}