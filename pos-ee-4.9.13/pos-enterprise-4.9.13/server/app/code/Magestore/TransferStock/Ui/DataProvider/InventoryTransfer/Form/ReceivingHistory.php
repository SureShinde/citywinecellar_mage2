<?php
/**
 * Copyright © 2019 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\TransferStock\Ui\DataProvider\InventoryTransfer\Form;

use Magestore\TransferStock\Model\ResourceModel\InventoryTransfer\Receive\CollectionFactory;
use Magento\Ui\DataProvider\AbstractDataProvider;

/**
 * Class ReceivingHistory
 * @package Magestore\InventorySuccess\Ui\DataProvider\TransferStock\Send\Form
 */
class ReceivingHistory extends AbstractDataProvider
{
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $request;

    /**
     * ReceivingHistory constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param \Magento\Framework\App\RequestInterface $request
     * @param CollectionFactory $collectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Framework\App\RequestInterface $request,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->request = $request;
        $this->collection = $collectionFactory->create();
        $this->prepareCollection();
    }

    /**
     * Prepare collection
     */
    public function prepareCollection()
    {
        $inventorytransfer_id = $this->request->getParam('inventorytransfer_id');
        if($inventorytransfer_id){
            $this->collection->addFieldToFilter("inventorytransfer_id", $inventorytransfer_id);
        }
    }
}
