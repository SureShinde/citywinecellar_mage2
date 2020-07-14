<?php
/**
 * Copyright © 2019 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\TransferStock\Ui\DataProvider\InventoryTransfer\Receive;

/**
 * Class ReceiveProduct
 * @package Magestore\TransferStock\Ui\DataProvider\InventoryTransfer\Receive
 */
class ReceiveProduct extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * @var \Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\ReceiveProduct\Collection
     */
    protected $collection;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $session;

    /**
     * @var \Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\ReceiveProduct\CollectionFactory
     */
    protected $_collectionFactory;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $_objectManager;

    /**
     * ReceiveProduct constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param \Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\ReceiveProduct\CollectionFactory $collectionFactory
     * @param \Magento\Framework\Registry $registry
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\ReceiveProduct\CollectionFactory $collectionFactory,
        \Magento\Backend\Model\Session $session,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->session = $session;

        $this->_collectionFactory = $collectionFactory;
        $this->_objectManager = $objectManager;
        $this->collection = $this->getProductCollection();

    }

    /**
     * @return \Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\ReceiveProduct\Collection
     */
    public function getProductCollection()
    {
        $receiveId = $this->session->getData('current_receive_id');
        /** @var \Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\ReceiveProduct\Collection $collection */
        $collection = $this->_collectionFactory->create();
        $collection->getImageProduct();
        $collection->getProductType();
        $collection->addFieldToFilter("receive_id", $receiveId);
        return $collection;
    }

    /**
     * @return array
     */
    public function getData()
    {
        $data = parent::getData();
        if ($this->session->getData('current_receive_id')) {
            /** @var \Magento\Catalog\Helper\Image $imageCalalogHelper */
            $imageCalalogHelper = $this->_objectManager->get(\Magento\Catalog\Helper\Image::class);
            foreach ($data['items'] as &$item) {
                $item['qty'] = (float) $item['qty'];
                if(strpos($item['image_url'], 'no_selection') !== false) {
                    $item['image_url'] = $imageCalalogHelper->getDefaultPlaceholderUrl('thumbnail');
                }
            }
        }
        return $data;
    }

}
