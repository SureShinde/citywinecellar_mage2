<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Ui\DataProvider\Shift\Form;

use Magento\Ui\DataProvider\Modifier\PoolInterface;

class Shift extends \Magento\Ui\DataProvider\AbstractDataProvider {

    /**
     * @var \Magento\Framework\Registry
     */
    protected $registry;

    /**
     * @var PoolInterface
     */
    protected $pool;

    /**
     * Pos constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param PoolInterface $pool
     * @param \Magento\Framework\Registry $registry
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        PoolInterface $pool,
        \Magento\Framework\Registry $registry,
        \Magestore\Webpos\Model\ResourceModel\Shift\Shift\CollectionFactory $shiftCollectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $shiftCollectionFactory->create();
        $this->pool = $pool;
        $this->registry = $registry;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $shift = $this->registry->registry('current_shift');
        if($shift && $shift->getId()){
            $data = $shift->getData();
            $this->data[$shift->getId()] = $data;
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