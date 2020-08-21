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
 *
 * Adjust stock's grid collection
 */
class Collection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
    /**
     * @inheritdoc
     */
    protected $document = Document::class;

    /**
     * Collection constructor.
     *
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param string $mainTable
     * @param string $resourceModel
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function __construct(// phpcs:ignore Generic.CodeAnalysis.UselessOverridingMethod
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        $mainTable = 'os_adjuststock',
        $resourceModel = \Magestore\AdjustStock\Model\ResourceModel\AdjustStock::class
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    /**
     * @inheritDoc
     */
    public function getData()
    {
        $data = parent::getData();

        $om = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magento\Framework\App\RequestInterface $request */
        $request = $om->get(\Magento\Framework\App\RequestInterface::class);
        $options = $om->get(\Magestore\AdjustStock\Model\AdjustStock\Options\Status::class)
            ->toOptionHash();
        $metadataProvider = $om->get(\Magento\Ui\Model\Export\MetadataProvider::class);
        if (!method_exists($metadataProvider, 'getColumnOptions')) {
            if ($request->getParam('is_export')) {
                foreach ($data as &$item) {
                    $item['status'] = $options[$item['status']];
                }
            }
        }
        return $data;
    }

    /**
     * @inheritDoc
     */
    protected function _initSelect()
    {
        $this->getSelect()->from(['main_table' => $this->getMainTable()])
            ->columns(
                [
                    'status', 'created_by', 'created_at', 'adjuststock_code', 'source_name',
                    'source' => new \Zend_Db_Expr('CONCAT(source_name, " (",source_code,")")')
                ]
            );

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field == 'source') {
            $field = new \Zend_Db_Expr('CONCAT(source_name, " (",source_code,")")');
        }
        return parent::addFieldToFilter($field, $condition);
    }
}
