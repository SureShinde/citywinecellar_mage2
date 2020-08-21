<?php
/**
 * Copyright © Magestore, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosReports\Model\Statistics;

/**
 * Interface StatisticInterface
 *
 * Used to create Statistic Interface
 */
interface StatisticInterface
{

    /**
     * Get statistic id
     *
     * @return string
     */
    public function getStatisticId();

    /**
     * Get statistic title
     *
     * @return string
     */
    public function getStatisticTitle();

    /**
     * Get statistic comment
     *
     * @return string
     */
    public function getStatisticComment();
}
