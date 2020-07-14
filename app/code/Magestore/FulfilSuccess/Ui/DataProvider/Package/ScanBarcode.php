<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Ui\DataProvider\Package;

use Magestore\FulfilSuccess\Model\ResourceModel\Package\Package\CollectionFactory;

/**
 * Class ScanBarcode
 * @package Magestore\FulfilSuccess\Ui\DataProvider\PackRequest
 */
class ScanBarcode extends \Magestore\FulfilSuccess\Ui\DataProvider\PackRequest\AbstractProvider
{

    /**
     * ScanBarcode constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param \Magento\Framework\Api\Search\ReportingInterface $reporting
     * @param \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Api\FilterBuilder $filterBuilder
     * @param \Magento\Framework\UrlInterface $urlBuilder
     * @param CollectionFactory $collectionFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magento\Framework\Api\Search\ReportingInterface $reporting,
        \Magento\Framework\Api\Search\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Framework\App\RequestInterface $request,
        \Magento\Framework\Api\FilterBuilder $filterBuilder,
        \Magento\Framework\UrlInterface $urlBuilder,
        CollectionFactory $collectionFactory,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $urlBuilder,
            $meta,
            $data
        );
        $this->collection = $collectionFactory->create();
        $this->emptyCollection();
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        $this->emptyCollection();
        $this->loadedData = [];
        $item = $this->collection->getNewEmptyItem();
        $this->loadedData[$item->getId()] = $item->getData();
        $this->loadedData[$item->getId()]['scan_form']['urls']['package_detail'] = $this->urlBuilder->getUrl('fulfilsuccess/package/findPackage');;
        $this->loadedData[$item->getId()]['scan_form']['modal_id'] = '#package_detail_holder';
        return $this->loadedData;
    }
}