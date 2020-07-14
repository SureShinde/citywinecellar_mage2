<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Ui\DataProvider\SupplierPricingList\DataForm\PricingList;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
//use Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface;
use Magestore\SupplierSuccess\Model\ResourceModel\Supplier\PricingList\CollectionFactory;

/**
 * Class ReceivedProduct
 * @package Magestore\PurchaseOrderSuccess\Ui\DataProvider\PurchaseOrder\ReceivedProduct\Form
 */
class AddPricingList extends AbstractDataProvider
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * @var PoolInterface
     */
    protected $pool;

    /**
     * @var PurchaseOrderRepositoryInterface
     */
    protected $purchaseOrderRepositoryInterface;

    /**
     * Warehouse constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param PoolInterface $pool
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        PoolInterface $pool,
        CollectionFactory $collectionFactory,
//        PurchaseOrderRepositoryInterface $purchaseOrderRepositoryInterface,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->pool = $pool;
//        $this->purchaseOrderRepositoryInterface = $purchaseOrderRepositoryInterface;
        $this->request = $request;
        $this->collection = $collectionFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function getData1()
    {
        $purchaseId = $this->request->getParam('purchase_id');
        $purchaseOrder = $this->purchaseOrderRepositoryInterface->get($purchaseId);
        if($purchaseOrder && $purchaseOrder->getId()){
            $this->data[$purchaseOrder->getId()] = $purchaseOrder->getData();
        }
        
        /** @var ModifierInterface $modifier */
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $this->data = $modifier->modifyData($this->data);
        }

        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function getMeta()
    {
        $meta = parent::getMeta();

        /** @var ModifierInterface $modifier */
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $meta = $modifier->modifyMeta($meta);
        }
        return $meta;
    }

}