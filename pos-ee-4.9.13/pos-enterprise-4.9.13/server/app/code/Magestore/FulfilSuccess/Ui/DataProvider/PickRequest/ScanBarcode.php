<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Ui\DataProvider\PickRequest;

use Magestore\FulfilSuccess\Model\ResourceModel\PickRequest\PickRequest\CollectionFactory;
use Magestore\FulfilSuccess\Ui\DataProvider\PickRequest\AbstractProvider;
use Magestore\FulfilSuccess\Service\PickRequest\PickService;
use Magestore\FulfilSuccess\Api\Data\PickRequestInterface;

/**
 * Class ScanBarcode
 * @package Magestore\FulfilSuccess\Ui\DataProvider\PickRequest
 */
class ScanBarcode extends AbstractProvider
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
     * @param PickService $pickService
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
        PickService $pickService,
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
            $pickService,
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
        $this->loadedData[$item->getId()]['scan_form']['picking'][PickRequestInterface::PICK_REQUEST_ID] = $this->pickService->getPickingRequestId();
        $this->loadedData[$item->getId()]['scan_form']['picking'][PickRequestInterface::ORDER_INCREMENT_ID] = $this->pickService->getPickingOrderIncrementId();
        $this->loadedData[$item->getId()]['scan_form']['picking']['items'] = $this->pickService->getPickingItems();
        $this->loadedData[$item->getId()]['scan_form']['urls']['cancel_picking'] = $this->urlBuilder->getUrl('fulfilsuccess/pickRequest/cancelPicking');
        $this->loadedData[$item->getId()]['scan_form']['urls']['pick_order'] = $this->urlBuilder->getUrl('fulfilsuccess/pickRequest/pickOrder');
        $this->loadedData[$item->getId()]['scan_form']['urls']['pick_items'] = $this->urlBuilder->getUrl('fulfilsuccess/pickRequest/pickItems');
        $this->loadedData[$item->getId()]['scan_form']['urls']['print_order_items'] = $this->urlBuilder->getUrl('fulfilsuccess/pickRequest/printOrderItems', ['id' => $this->pickService->getPickingRequestId()]);
        $this->loadedData[$item->getId()]['scan_form']['listing']['items'] = 'os_fulfilsuccess_pickrequest_listing.os_fulfilsuccess_pickrequest_listing.pickrequest_scan_item_modal_container.pickrequest_scan_item_modal.os_fulfilsuccess_picked_listing';
        $this->loadedData[$item->getId()]['scan_form']['listing']['requests'] = 'os_fulfilsuccess_pickrequest_listing.pickrequest_listing_data_source';
        $this->loadedData[$item->getId()]['scan_form']['listing']['recent_picked'] = 'os_fulfilsuccess_pickrequest_listing.os_fulfilsuccess_pickrequest_listing.os_fulfilsuccess_recent_picked_container.os_fulfilsuccess_recent_picked_fieldset.os_fulfilsuccess_recent_picked_listing';
        return $this->loadedData;
    }
}