<?php
/**
 * Copyright © Magestore, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosReports\Model\ResourceModel\Report;

/**
 * Class PosOrder
 *
 * Used to create POS Order entity resource model
 */
class PosOrder extends \Magento\Reports\Model\ResourceModel\Report\AbstractReport
{
    /**
     * @var \Magestore\PosReports\Model\ResourceModel\Report\PosOrder\CreatedatFactory
     */
    protected $_createDatFactory;

    /**
     * @var \Magestore\PosReports\Model\ResourceModel\Report\PosOrder\UpdatedatFactory
     */
    protected $_updateDatFactory;

    /**
     * PosOrder constructor.
     *
     * @param \Magento\Framework\Model\ResourceModel\Db\Context $context
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Reports\Model\FlagFactory $reportsFlagFactory
     * @param \Magento\Framework\Stdlib\DateTime\Timezone\Validator $timezoneValidator
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param PosOrder\CreatedatFactory $createDatFactory
     * @param PosOrder\UpdatedatFactory $updateDatFactory
     * @param string|null $connectionName
     */
    public function __construct(
        \Magento\Framework\Model\ResourceModel\Db\Context $context,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Reports\Model\FlagFactory $reportsFlagFactory,
        \Magento\Framework\Stdlib\DateTime\Timezone\Validator $timezoneValidator,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        PosOrder\CreatedatFactory $createDatFactory,
        PosOrder\UpdatedatFactory $updateDatFactory,
        $connectionName = null
    ) {
        parent::__construct(
            $context,
            $logger,
            $localeDate,
            $reportsFlagFactory,
            $timezoneValidator,
            $dateTime,
            $connectionName
        );
        $this->_createDatFactory = $createDatFactory;
        $this->_updateDatFactory = $updateDatFactory;
    }

    /**
     * Model initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('pos_order_aggregated_created', 'id');
    }

    /**
     * Aggregate POS Orders data
     *
     * @param string|int|\DateTime|array|null $from
     * @param string|int|\DateTime|array|null $to
     * @return $this
     */
    public function aggregate($from = null, $to = null)
    {
        $this->_createDatFactory->create()->aggregate($from, $to);
        $this->_updateDatFactory->create()->aggregate($from, $to);
        $this->_setFlagData(\Magestore\PosReports\Model\Flag::REPORT_POS_SALES_FLAG_CODE);
        return $this;
    }
}
