<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Model\ResourceModel\Product\Supplier;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface as FetchStrategy;
use Magento\Framework\Data\Collection\EntityFactoryInterface as EntityFactory;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Psr\Log\LoggerInterface as Logger;
use Magento\Framework\App\RequestInterface;


/**
 * Class Collection
 * @package Magestore\SupplierSuccess\Model\ResourceModel\Supplier
 */
class Collection extends \Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult
{
    const MAPPING_FIELDS = [
        'supplier_id' => 'main_table.supplier_id'
    ];

    /**
     * @var RequestInterface
     */
    protected $request;
    
    /**
     * Initialize dependencies.
     *
     * @param EntityFactory $entityFactory
     * @param Logger $logger
     * @param FetchStrategy $fetchStrategy
     * @param EventManager $eventManager
     * @param string $mainTable
     * @param string $resourceModel
     */
    public function __construct(
        EntityFactory $entityFactory,
        Logger $logger,
        FetchStrategy $fetchStrategy,
        EventManager $eventManager,
        RequestInterface $request,
        $mainTable = 'os_supplier',
        $resourceModel = 'Magestore\SupplierSuccess\Model\ResourceModel\Supplier'
    ) {
        $this->request = $request;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $mainTable, $resourceModel);
    }

    protected function _initSelect()
    {
        $condition = 'main_table.supplier_id = supplier_product.supplier_id';
        if($productId = $this->request->getParam('id')){
            $condition.=" AND supplier_product.product_id = $productId";
        }
        $this->getSelect()->from(['main_table' => $this->getMainTable()])
            ->joinInner(
                ['supplier_product' => $this->getTable('os_supplier_product')],
                $condition,
                ['product_supplier_sku', 'cost', 'tax']
            )->group('main_table.supplier_id');
        return $this;
    }

    public function addFieldToFilter($field, $condition = null)
    {
        if(in_array($field, array_keys(self::MAPPING_FIELDS)))
            $field = new \Zend_Db_Expr(self::MAPPING_FIELDS[$field]);
        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Add select order
     *
     * @param   string $field
     * @param   string $direction
     * @return  $this
     */
    public function setOrder($field, $direction = self::SORT_ORDER_DESC)
    {
        if(in_array($field, array_keys(self::MAPPING_FIELDS)))
            $field = new \Zend_Db_Expr(self::MAPPING_FIELDS[$field]);
        return parent::setOrder($field, $direction);
    }
}