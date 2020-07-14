<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\Config\Backend;

use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;

/**
 * Class CollectionTime
 *
 * @package Magestore\Webpos\Model\Config\Backend
 */
class CollectionTime extends Value
{
    /**
     * The path to config setting of schedule of collection data cron.
     */
    const CRON_SCHEDULE_PATH = 'crontab/default/jobs/webpos_order_converter/schedule/cron_expr';

    /**
     * @var \Magento\Framework\App\Config\ValueFactory
     */
    protected $_configValueFactory;

    /**
     * CollectionTime constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param ScopeConfigInterface $config
     * @param TypeListInterface $cacheTypeList
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param \Magento\Framework\App\Config\ValueFactory $configValueFactory
     * @param array $data
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        Context $context,
        Registry $registry,
        ScopeConfigInterface $config,
        TypeListInterface $cacheTypeList,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        \Magento\Framework\App\Config\ValueFactory $configValueFactory,
        array $data = []
    ) {
        $this->_configValueFactory = $configValueFactory;
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
