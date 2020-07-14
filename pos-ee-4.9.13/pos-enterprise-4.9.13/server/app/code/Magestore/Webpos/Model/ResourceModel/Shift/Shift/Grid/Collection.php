<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Model\ResourceModel\Shift\Shift\Grid;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;

use Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult;
use Magestore\Webpos\Api\Data\Shift\ShiftInterface;

/**
 * Class Collection
 * @package Magestore\Webpos\Model\ResourceModel\Shift\Shift\Grid
 */
class Collection extends SearchResult
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $requestInterface;

    /**
     * Collection constructor.
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param \Magento\Framework\App\RequestInterface $requestInterface
     * @param string $mainTable
     * @param string $resourceModel
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        \Magento\Framework\App\RequestInterface $requestInterface,
        $mainTable = 'webpos_shift',
        $resourceModel = 'Magestore\Webpos\Model\ResourceModel\Shift\Shift'
    ) {
        $this->requestInterface = $requestInterface;
        parent::__construct(
            $entityFactory,
            $logger,
            $fetchStrategy,
            $eventManager,
            $mainTable,
            $resourceModel
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function _initSelect()
    {
        $posId = $this->requestInterface->getParam('pos_id', false);
        parent::_initSelect();
        $this->addFieldToFilter(ShiftInterface::STATUS, ShiftInterface::CLOSE_STATUS);
        if($posId){
            $this->addFieldToFilter(ShiftInterface::POS_ID, $posId);
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getData()
    {
        $om = \Magento\Framework\App\ObjectManager::getInstance();
        $data = parent::getData();
        /** @var \Magento\Framework\App\RequestInterface $request */
        $request = $om->get('Magento\Framework\App\RequestInterface');
        if($request->getParam('is_export')) {
            $staffData = $om->get('Magestore\Webpos\Ui\Component\Listing\Column\Staff')
                ->getOptionArray();
            foreach ($data as &$item) {
                $item['staff_id'] = $staffData[$item['staff_id']];
            }
        }
        return $data;
    }
}