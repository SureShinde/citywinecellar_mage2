<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Ui\DataProvider\Pos\Form;

use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magestore\Webpos\Model\ResourceModel\Pos\Pos\CollectionFactory;
use Magestore\Webpos\Model\ResourceModel\Pos\Pos\Collection;

class Pos extends \Magento\Ui\DataProvider\AbstractDataProvider {

    /**
     * @var \Magento\Framework\Registry
     */
    protected $_coreRegistry;

    /**
     * @var Collection
     */
    protected $collection;

    /**
     * @var PoolInterface
     */
    protected $pool;

    /**
     * Staff constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param PoolInterface $pool
     * @param \Magento\Framework\Registry $registry
     * @param CollectionFactory $collectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        PoolInterface $pool,
        \Magento\Framework\Registry $registry,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->pool = $pool;
        $this->_coreRegistry = $registry;
        $this->collection = $collectionFactory->create();
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $pos = $this->_coreRegistry->registry('current_pos');
        if($pos && $pos->getId()){
            $data = $pos->getData();
            $this->data[$pos->getId()] = $data;
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