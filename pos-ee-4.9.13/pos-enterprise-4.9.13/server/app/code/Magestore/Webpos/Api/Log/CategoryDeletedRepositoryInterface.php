<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Log;

/**
 * Interface CategoryDeletedRepositoryInterface
 * @package Magestore\Webpos\Api\Log
 */
interface CategoryDeletedRepositoryInterface {

    /**
     * @param \Magestore\Webpos\Api\Data\Log\CategoryDeletedInterface $categoryDeleted
     * @return \Magestore\Webpos\Api\Data\Log\CategoryDeletedInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Magestore\Webpos\Api\Data\Log\CategoryDeletedInterface $categoryDeleted);
    /**
     * @param \Magestore\Webpos\Api\Data\Log\CategoryDeletedInterface $categoryDeleted
     * @return boolean
     */
    public function delete(\Magestore\Webpos\Api\Data\Log\CategoryDeletedInterface $categoryDeleted);

    /**
     * @param int $categoryId
     * @return boolean
     */
    public function deleteById($categoryId);

}
