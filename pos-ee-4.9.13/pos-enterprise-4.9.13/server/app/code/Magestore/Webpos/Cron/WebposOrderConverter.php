<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Cron;

use Magestore\Webpos\Model\Checkout\PosOrder;

/**
 * Class WebposOrderConverter
 *
 * @package Magestore\Webpos\Cron
 */
class WebposOrderConverter
{
    /**
     * @var \Magestore\Webpos\Api\Checkout\PosOrderRepositoryInterface
     */
    protected $posOrderRepository;
    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Checkout\PosOrder\Collection
     */
    protected $posOrderCollection;
    /**
     * @var \Magestore\Webpos\Model\ResourceModel\Request\ActionLog\CollectionFactory
     */
    protected $actionLogCollectionFactory;

    /**
     * WebposOrderConverter constructor.
     *
     * @param \Magestore\Webpos\Api\Checkout\PosOrderRepositoryInterface $posOrderRepository
     * @param \Magestore\Webpos\Model\ResourceModel\Checkout\PosOrder\Collection $posOrderCollection
     * @param \Magestore\Webpos\Model\ResourceModel\Request\ActionLog\CollectionFactory $actionLogCollectionFactory
     */
    public function __construct(
        \Magestore\Webpos\Api\Checkout\PosOrderRepositoryInterface $posOrderRepository,
        \Magestore\Webpos\Model\ResourceModel\Checkout\PosOrder\Collection $posOrderCollection,
        \Magestore\Webpos\Model\ResourceModel\Request\ActionLog\CollectionFactory $actionLogCollectionFactory
    ) {
        $this->posOrderRepository = $posOrderRepository;
        $this->posOrderCollection = $posOrderCollection;
        $this->actionLogCollectionFactory = $actionLogCollectionFactory;
    }

    /**
     * Process in-completed orders
     *
     * @throws \Exception
     */
    public function execute()
    {
        $posOrderCollection = $this->posOrderCollection
            ->addFieldToFilter('status', ['neq' => PosOrder::STATUS_COMPLETED]);

        foreach ($posOrderCollection->getItems() as $posOrder) {
            $this->posOrderRepository->processConvertOrder($posOrder->getIncrementId());
        }

        /** @var \Magestore\Webpos\Model\ResourceModel\Request\ActionLog\Collection $actionLogCollection */
        $actionLogCollection = $this->actionLogCollectionFactory->create();
        $actionLogCollection->addFieldToFilter(
            'status',
            ['neq' => \Magestore\Webpos\Model\Request\ActionLog::STATUS_COMPLETED]
        );

        $actionLogCollection->processActionLog();
    }
}
