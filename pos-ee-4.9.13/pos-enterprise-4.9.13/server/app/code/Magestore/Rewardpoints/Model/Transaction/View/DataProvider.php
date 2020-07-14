<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Rewardpoints\Model\Transaction\View;

use Magestore\Rewardpoints\Model\ResourceModel\Transaction\CollectionFactory;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @var \Magento\Framework\App\RequestInterface
     */

    protected $request;

    /**
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $transactionCollectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $transactionCollectionFactory,
        \Magento\Framework\App\RequestInterface $request,
        array $meta = [],
        array $data = []
    ) {
        $this->collection = $transactionCollectionFactory->create();
        $this->request = $request;
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
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
        $items = $this->collection->getItems();
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        $resourceActions = $objectManager->create('Magestore\Rewardpoints\Ui\Component\Listing\Column\Transaction\Actions');
        $resourceStoreView = $objectManager->create('Magestore\Rewardpoints\Ui\Component\Listing\Column\Transaction\StoreView');
        foreach ($items as $transaction) {
            $transaction->setAction($resourceActions->toOptionHash()[$transaction->getAction()]);
            $transaction->setStatus( $transaction->getStatusHash()[$transaction->getStatus()]);

            $transaction->setPointAmount( $transaction->getPointAmount().__(' Points') );
            $transaction->setPointUsed( $transaction->getPointUsed().__(' Points') );

            $transaction->setCreatedTime(date('F j, Y g:i A',strtotime($transaction->getCreatedTime())));
            $updatedTime = ($transaction->getUpdatedTime())?$transaction->getUpdatedTime():$transaction->getCreatedTime();
            $transaction->setUpdatedAt(date('F j, Y g:i A',strtotime($updatedTime)));
            $transaction->setStoreView($resourceStoreView->toOptionHash()[$transaction->getStoreId()]);
            $result[$transaction->getId()] = $transaction->getData();
            $this->loadedData = $result;
        }
        return $this->loadedData;
    }

}
