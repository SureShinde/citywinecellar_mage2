<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Log;

/**
 * Interface DataLogResultsInterface
 * @package Magestore\Webpos\Api\Data\Log
 */
interface DataLogResultsInterface {
    /**
     *
     */
    const IDS = 'ids';
    /**
     * Get ids
     *
     * @return int[]
     */
    public function getIds();
    /**
     * Set ids
     *
     * @param array $ids
     * @return DataLogResultsInterface
     */
    public function setIds($ids);
}