<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\AdjustStock\Model\ResourceModel\AdjustStock\Grid;

use Magento\Customer\Ui\Component\DataProvider\Document;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;

/**
 * Class Collection
 * @package Magestore\AdjustStock\Model\ResourceModel\AdjustStock\Grid
 */
class Collection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
    /**
     * @inheritdoc
     */
    protected $document = Document::class;

    /**
     * Collection constructor.
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param string $mainTable
     * @param string $resourceModel
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        $mainTable = 'os_adjuststock',
        $resourceModel = 'Magestore\AdjustStock\Model\ResourceModel\AdjustStock'
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    public function getData()
    {
        $data = parent::getData();

        $om = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magento\Framework\App\RequestInterface $request */
        $request = $om->get('Magento\Framework\App\RequestInterface');
        $options = $om->get('Magestore\AdjustStock\Model\AdjustStock\Options\Status')
            ->toOptionHash();
        if($request->getParam('is_export')) {
            foreach ($data as &$item) {
                $item['status'] = $options[$item['status']];
            }
        }

        return $data;
    }

    /**
     * @return $this|\Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult|void
     */
    protected function _initSelect()
    {
        $this->getSelect()->from(['main_table' => $this->getMainTable()])
            ->columns(
                [
                    'status', 'created_by', 'created_at', 'adjuststock_code', 'source_name',
                    'source' => new \Zend_Db_Expr('CONCAT(source_name, " (",source_code,")")')
                ]);

        return $this;
    }

    /**
     * @param array|string $field
     * @param null $condition
     * @return \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'source') {
            $field = new \Zend_Db_Expr('CONCAT(source_name, " (",source_code,")")');
        }
        return parent::addFieldToFilter($field, $condition);
    }
}
