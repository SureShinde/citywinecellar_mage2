<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PurchaseOrderSuccess\Ui\DataProvider\ReturnOrder\TransferredProduct\Form;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magestore\PurchaseOrderSuccess\Api\ReturnOrderRepositoryInterface;
use Magestore\PurchaseOrderSuccess\Model\ResourceModel\ReturnOrder\CollectionFactory;

/**
 * Class TransferredProduct
 * @package Magestore\PurchaseOrderSuccess\Ui\DataProvider\ReturnOrder\ReceivedProduct\Form
 */
class TransferredProduct extends AbstractDataProvider
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
     * @var ReturnOrderRepositoryInterface
     */
    protected $returnOrderRepositoryInterface;

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
        ReturnOrderRepositoryInterface $returnOrderRepositoryInterface,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->pool = $pool;
        $this->returnOrderRepositoryInterface = $returnOrderRepositoryInterface;
        $this->request = $request;
        $this->collection = $collectionFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $returnId = $this->request->getParam('return_id');
        $returnOrder = $this->returnOrderRepositoryInterface->get($returnId);
        if($returnOrder && $returnOrder->getReturnOrderId()){
            $this->data[$returnOrder->getReturnOrderId()] = $returnOrder->getData();
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