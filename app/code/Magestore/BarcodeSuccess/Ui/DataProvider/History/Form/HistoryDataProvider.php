<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\BarcodeSuccess\Ui\DataProvider\History\Form;

use Magestore\BarcodeSuccess\Model\ResourceModel\History\CollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;
use Magento\Ui\DataProvider\Modifier\PoolInterface;

/**
 * DataProvider for product edit form
 */
class HistoryDataProvider extends AbstractDataProvider
{
    /**
     * @var PoolInterface
     */
    private $pool;

    /**
     * @var \Magestore\BarcodeSuccess\Helper\Data
     */
    private $helper;

    /**
     * HistoryDataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param PoolInterface $pool
     * @param \Magestore\BarcodeSuccess\Helper\Data $helper
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        PoolInterface $pool,
        \Magestore\BarcodeSuccess\Helper\Data $helper,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->pool = $pool;
        $this->collection->getSelect()->joinLeft(array('admin_user' => $this->collection->getTable('admin_user'))
            , 'main_table.created_by = admin_user.user_id', array('username'));
        if(isset($data['type_provider']) && $data['type_provider']) {
            $this->type_provider = $data['type_provider'];
        }
        $this->helper = $helper;
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        if($this->type_provider == 'form') {
            $items = $this->collection->getItems();
            foreach ($items as $item) {
                $this->loadedData[$item->getId()] = $item->getData();
                if(isset($this->loadedData[$item->getId()]['created_at'])){
                    $this->loadedData[$item->getId()]['created_at'] = $this->helper->formatDate($this->loadedData[$item->getId()]['created_at']);
                }
            }

            if (!empty($data)) {
                $item = $this->collection->getNewEmptyItem();
                $item->setData($data);
                $this->loadedData[$item->getId()] = $item->getData();
                if(isset($this->loadedData[$item->getId()]['created_at'])){
                    $this->loadedData[$item->getId()]['created_at'] = $this->helper->formatDate($this->loadedData[$item->getId()]['created_at']);
                }
            }
        }else{
            $this->loadedData = $this->getCollection()->toArray();
        }
        foreach ($this->pool->getModifiersInstances() as $modifier) {
            $this->loadedData = $modifier->modifyData($this->loadedData);
        }
        return $this->loadedData;
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
