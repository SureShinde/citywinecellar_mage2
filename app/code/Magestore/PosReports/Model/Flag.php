<?php
/**
 * Copyright © Magestore, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosReports\Model;

/**
 * Used to create Report Flag Model
 *
 * Class Flag
 */
class Flag extends \Magento\Reports\Model\Flag
{

    const REPORT_POS_SALES_FLAG_CODE = 'report_pos_sales_aggregated';
    const REPORT_POS_SALES_BY_PAYMENT_FLAG_CODE = 'report_pos_sales_by_payment_aggregated';

    /**
     * Get pos statistic flag code
     *
     * @param Statistics\StatisticInterface $posStatistic
     * @return string
     */
    public function getPosStatisticFlagCode(Statistics\StatisticInterface $posStatistic)
    {
        return "report_" . $posStatistic->getStatisticId() . "_aggregated";
    }

    /**
     * Get pos statistic updated at
     *
     * @param Statistics\StatisticInterface $posStatistic
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPosStatisticUpdatedAt(Statistics\StatisticInterface $posStatistic)
    {
        $reportCode = $this->getPosStatisticFlagCode($posStatistic);
        $flag = $this->setReportFlagCode($reportCode)->loadSelf();
        return $flag->hasData() ? $flag->getLastUpdate() : '';
    }
}
