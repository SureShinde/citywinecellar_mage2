<?php
/**
 * Copyright © Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosReports\Controller\Adminhtml\Reports;

use Magestore\PosReports\Model\Statistics\StatisticInterface;

/**
 * Class AbstractAction
 *
 * Used to create Abstract Action
 */
abstract class AbstractAction extends \Magestore\PosReports\Controller\Adminhtml\AbstractAction
{
    /**
     * @var string
     */
    protected $resource = 'Magestore_PosReports::report_listing';

    /**
     * @var \Magestore\PosReports\Model\FlagFactory
     */
    protected $flagFactory;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $timezone;

    /**
     * @var StatisticInterface
     */
    protected $statistic;

    /**
     * AbstractAction constructor.
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magestore\PosReports\Model\FlagFactory $flagFactory
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param StatisticInterface $statistic
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $registry,
        \Magestore\PosReports\Model\FlagFactory $flagFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        StatisticInterface $statistic
    ) {
        parent::__construct($context, $registry);
        $this->flagFactory = $flagFactory;
        $this->timezone = $timezone;
        $this->statistic = $statistic;
    }

    /**
     * Execute
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface|mixed
     */
    public function execute()
    {
        $this->_showLastExecutionTime();
        return $this->createPageResult();
    }

    /**
     * Add refresh statistics links
     *
     * @return $this
     */
    protected function _showLastExecutionTime()
    {
        $statistic = $this->statistic;
        $flag = $this->flagFactory->create();
        $updatedAt = $flag->getPosStatisticUpdatedAt($statistic);
        if ($updatedAt) {
            $updatedAt = $this->timezone->formatDate(
                $updatedAt,
                \IntlDateFormatter::MEDIUM,
                true
            );
        } else {
            $updatedAt = 'undefined';
        }

        $refreshStatsLink = $this->getUrl('reports/report_statistics');
        $directRefreshLink = $this->getUrl('reports/report_statistics/refreshRecent');

        $this->messageManager->addNotice(
            __(
                'Last updated: %1. To refresh last day\'s <a href="%2">statistics</a>, ' .
                'click <a href="#2" data-post="%3">here</a>.',
                $updatedAt,
                $refreshStatsLink,
                str_replace(
                    '"',
                    '&quot;',
                    json_encode(['action' => $directRefreshLink, 'data' => ['code' => $statistic->getStatisticId()]])
                )
            )
        );
        return $this;
    }
}
