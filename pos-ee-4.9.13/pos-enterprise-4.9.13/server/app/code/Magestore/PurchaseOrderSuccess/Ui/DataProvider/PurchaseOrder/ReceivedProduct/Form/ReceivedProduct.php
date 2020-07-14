<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Ui\DataProvider\PurchaseOrder\ReceivedProduct\Form;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magestore\PurchaseOrderSuccess\Api\PurchaseOrderRepositoryInterface;
use Magestore\PurchaseOrderSuccess\Model\ResourceModel\PurchaseOrder\CollectionFactory;

/**
 * Class ReceivedProduct
 * @package Magestore\PurchaseOrderSuccess\Ui\DataProvider\PurchaseOrder\ReceivedProduct\Form
 */
class ReceivedProduct extends AbstractDataProvider
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
        PurchaseOrderRepositoryInterface $purchaseOrderRepositoryInterface,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->pool = $pool;
        $this->purchaseOrderRepositoryInterface = $purchaseOrderRepositoryInterface;
        $this->request = $request;
        $this->collection = $collectionFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
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
//        \Zend_Debug::dump($meta);die;
        return $meta;
    }

}