<?php
/**
 * Copyright © Magestore, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\PosReports\Cron;

/**
 * Class AggregateSalesReportPosOrderData
 *
 * Used to create Aggregate Sales Report Pos Order Data
 */
class AggregateSalesReportPosOrderData
{
    /**
     * @var \Magento\Framework\Locale\ResolverInterface
     */
    protected $localeResolver;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $localeDate;

    /**
     * @var \Magestore\PosReports\Model\ResourceModel\Report\PosOrderFactory
     */
    protected $posOrderFactory;

    /**
     * AggregateSalesReportPosOrderData constructor.
     *
     * @param \Magento\Framework\Locale\ResolverInterface $localeResolver
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone
     * @param \Magestore\PosReports\Model\ResourceModel\Report\PosOrderFactory $posOrderFactory
     */
    public function __construct(
        \Magento\Framework\Locale\ResolverInterface $localeResolver,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $timezone,
        \Magestore\PosReports\Model\ResourceModel\Report\PosOrderFactory $posOrderFactory
    ) {
        $this->localeResolver = $localeResolver;
        $this->localeDate = $timezone;
        $this->posOrderFactory = $posOrderFactory;
    }

    /**
     * Refresh POS sales order report statistics for last day
     *
     * @throws \Exception
     */
    public function execute()
    {
        $this->localeResolver->emulate(0);
        $currentDate = $this->localeDate->date();
        $date = $currentDate->sub(new \DateInterval('PT25H'));
        $this->posOrderFactory->create()->aggregate($date);
        $this->localeResolver->revert();
    }
}
