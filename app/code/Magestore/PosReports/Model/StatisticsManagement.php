<?php
/**
 * Copyright © Magestore, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosReports\Model;

/**
 * Class StatisticsManagement
 *
 * Used to create Statistics Management
 */
class StatisticsManagement
{
    /**
     * @var \Magestore\PosReports\Model\FlagFactory
     */
    protected $reportsFlagFactory;

    /**
     * @var Statistics\StatisticInterface[]
     */
    protected $posStatistics;

    /**
     * StatisticsManagement constructor.
     *
     * @param FlagFactory $reportsFlagFactory
     * @param Statistics\StatisticInterface[] $posStatistics
     */
    public function __construct(
        FlagFactory $reportsFlagFactory,
        $posStatistics = []
    ) {
        $this->reportsFlagFactory = $reportsFlagFactory;
        $this->posStatistics = $posStatistics;
    }

    /**
     * Get statistics
     *
     * @return Statistics\StatisticInterface[]
     */
    public function getStatistics()
    {
        $posStatistics = [];
        if (!empty($this->posStatistics)) {
            foreach ($this->posStatistics as $posStatistic) {
                if ($posStatistic instanceof Statistics\StatisticInterface) {
                    $posStatistics[] = $posStatistic;
                }
            }
        }
        return $posStatistics;
    }

    /**
     * Convert to item
     *
     * @param Statistics\StatisticInterface $posStatistic
     * @return \Magento\Framework\DataObject
     */
    public function convertToItem(Statistics\StatisticInterface $posStatistic)
    {
        $item = new \Magento\Framework\DataObject();
        $item->setData(
            [
                'id' => $posStatistic->getStatisticId(),
                'report' => $posStatistic->getStatisticTitle(),
                'comment' => $posStatistic->getStatisticComment(),
                'updated_at' => $this->reportsFlagFactory->create()->getPosStatisticUpdatedAt($posStatistic)
            ]
        );
        return $item;
    }
}
