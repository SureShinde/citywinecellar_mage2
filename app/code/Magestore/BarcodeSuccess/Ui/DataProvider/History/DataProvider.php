<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\BarcodeSuccess\Ui\DataProvider\History;

use Magestore\BarcodeSuccess\Model\ResourceModel\History\CollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * Class DataProvider
 *
 * Used to create data provider
 */
class DataProvider extends AbstractDataProvider
{
    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @var string
     */
    protected $type_provider;

    /**
     * Generate constructor.
     *
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->collection = $collectionFactory->create();
        $this->collection->getSelect()->joinLeft(
            ['admin_user' => $this->collection->getTable('admin_user')],
            'main_table.created_by = admin_user.user_id',
            ['username']
        );

        if (isset($data['type_provider']) && $data['type_provider']) {
            $this->type_provider = $data['type_provider'];
        }
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        if ($this->type_provider == 'form') {
            $items = $this->collection->getItems();
            foreach ($items as $item) {
                $this->loadedData[$item->getId()] = $item->getData();
            }

            if (!empty($data)) {
                $item = $this->collection->getNewEmptyItem();
                $item->setData($data);
                $this->loadedData[$item->getId()] = $item->getData();
            }
        } else {
            $this->loadedData = $this->getCollection()->toArray();
        }
        return $this->loadedData;
    }
}
