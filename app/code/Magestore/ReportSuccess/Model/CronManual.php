<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */
namespace Magestore\ReportSuccess\Model;
/**
 * Class CronManual
 * @package Magestore\ReportSuccess\Model
 */
class CronManual extends \Magento\Framework\Model\AbstractModel
{
    /**
     * CronManual constructor.
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param ResourceModel\CronManual $resource
     * @param ResourceModel\CronManual\Collection $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magestore\ReportSuccess\Model\ResourceModel\CronManual $resource,
        \Magestore\ReportSuccess\Model\ResourceModel\CronManual\Collection $resourceCollection,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }
}