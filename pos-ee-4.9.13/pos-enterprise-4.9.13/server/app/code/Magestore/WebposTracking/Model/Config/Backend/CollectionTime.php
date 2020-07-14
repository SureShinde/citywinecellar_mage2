<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\WebposTracking\Model\Config\Backend;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

/**
 * Config value backend model.
 */
class CollectionTime extends Value
{
    /**
     * The path to config setting of schedule of collection data cron.
     */
    const CRON_SCHEDULE_PATH = 'crontab/default/jobs/syncing_tracking_data/schedule/cron_expr';
    /**
     * @var WriterInterface
     */
    private $configWriter;

    /**
     * @var \Magento\Framework\App\Config\ValueFactory
     */
    protected $_configValueFactory;
    /**
     * @var \Magestore\WebposTracking\Helper\Data
     */
    protected $helper;

    /**
     * CollectionTime constructor.
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param WriterInterface $configWriter
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param \Magento\Framework\App\Config\ValueFactory $configValueFactory
     * @param \Magestore\WebposTracking\Helper\Data $helper
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        WriterInterface $configWriter,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        \Magento\Framework\App\Config\ValueFactory $configValueFactory,
        \Magestore\WebposTracking\Helper\Data $helper,
        array $data = []
    ) {
        $this->configWriter = $configWriter;
        $this->_configValueFactory = $configValueFactory;
        $this->helper = $helper;
        parent::__construct($context, $registry, $config, $cacheTypeList, $resource, $resourceCollection, $data);
    }
    /**
     * {@inheritdoc}. Set schedule setting for cron.
     *
     * @return Value
     * @throws LocalizedException
     */
    public function afterSave()
    {
        $timeConfig = $this->getValue();
        if (!$timeConfig) {
            $timeConfig = '00,00,00';
        }
        preg_match('#(?<hour>\d{2}),(?<min>\d{2}),(?<sec>\d{2})#', $timeConfig, $time);

        $cronExprArray = [
            (int)$time['min'], //Minute
            (int)$time['hour'], //Hour
            '*', //Day of the Month
            '*', //Month of the Year
            '*', //Day of the Week
        ];

        $cronExprString = join(' ', $cronExprArray);

        try {
            $this->_configValueFactory->create()->load(
                self::CRON_SCHEDULE_PATH,
                'path'
            )->setValue(
                $cronExprString
            )->setPath(
                self::CRON_SCHEDULE_PATH
            )->save();
        } catch (\Exception $e) {
            throw new LocalizedException(__('We can\'t save the cron expression.'));
        }

        return parent::afterSave();
    }
}
