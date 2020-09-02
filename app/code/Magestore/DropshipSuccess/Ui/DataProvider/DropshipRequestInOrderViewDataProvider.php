<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Ui\DataProvider;
use Magestore\DropshipSuccess\Model\ResourceModel\DropshipRequest\Grid\CollectionFactory;
use Magento\Framework\Api\FilterBuilder;
/**
 * Class ProductDataProvider
 */
class DropshipRequestInOrderViewDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /** @var mixed  */
    protected $collection;
    /**
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $requestInterface;
    /**
     * @var FilterBuilder
     */
    protected $filterBuilder;
    /**
     * DropshipRequestInOrderViewDataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param \Magento\Framework\App\RequestInterface $requestInterface
     * @param FilterBuilder $filterBuilder
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        \Magento\Framework\App\RequestInterface $requestInterface,
        FilterBuilder $filterBuilder,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->requestInterface = $requestInterface;
        $this->filterBuilder = $filterBuilder;
        $orderId = $this->requestInterface->getParam('order_id');
        $this->collection = $collectionFactory->create()->addFieldToFilter('order_id', $orderId);
        $this->prepareUpdateUrl();
    }
    /**
     * @return void
     */
    public function prepareUpdateUrl()
    {
        if (!isset($this->data['config']['filter_url_params'])) {
            return;
        }
        foreach ($this->data['config']['filter_url_params'] as $paramName => $paramValue) {
            if ('*' == $paramValue) {
                $paramValue = $this->requestInterface->getParam($paramName);
            }
            if ($paramValue) {
                $this->data['config']['update_url'] = sprintf(
                    '%s%s/%s/',
                    $this->data['config']['update_url'],
                    $paramName,
                    $paramValue
                );
//                $this->addFilter(
//                    $this->filterBuilder->setField($paramName)->setValue($paramValue)->setConditionType('eq')->create()
//                );
            }
        }
    }
}