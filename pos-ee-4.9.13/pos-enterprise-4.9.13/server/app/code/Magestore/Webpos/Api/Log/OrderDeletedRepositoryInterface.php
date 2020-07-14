<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Log;

/**
 * Interface OrderDeletedRepositoryInterface
 * @package Magestore\Webpos\Api\Log
 */
interface OrderDeletedRepositoryInterface {

    /**
     * @param \Magestore\Webpos\Api\Data\Log\OrderDeletedInterface $orderDeleted
     * @return \Magestore\Webpos\Api\Data\Log\OrderDeletedInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Magestore\Webpos\Api\Data\Log\OrderDeletedInterface $orderDeleted);

    /**
     * @param \Magestore\Webpos\Api\Data\Log\OrderDeletedInterface $orderDeleted
     * @return boolean
     */
    public function delete(\Magestore\Webpos\Api\Data\Log\OrderDeletedInterface $orderDeleted);

    /**
     * @param int $orderId
     * @return boolean
     */
    public function deleteById($orderId);
}