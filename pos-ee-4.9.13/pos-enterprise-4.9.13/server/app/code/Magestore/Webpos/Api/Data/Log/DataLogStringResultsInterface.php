<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Api\Data\Log;

/**
 * Interface DataLogStringResultsInterface
 * @package Magestore\Webpos\Api\Data\Log
 */
interface DataLogStringResultsInterface {
    /**
     *
     */
    const IDS = 'ids';
    /**
     * Get ids
     *
     * @return string[]
     */
    public function getIds();
    /**
     * Set ids
     *
     * @param array $ids
     * @return DataLogStringResultsInterface
     */
    public function setIds($ids);
}