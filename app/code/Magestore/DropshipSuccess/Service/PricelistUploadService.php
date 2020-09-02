<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\DropshipSuccess\Service;

use Magestore\DropshipSuccess\Api\Data\SupplierPricelistUploadInterface;
use Magestore\DropshipSuccess\Model\ResourceModel\Supplier\PricelistUpload\CollectionFactory as PricelistUploadCollectionFactory;

class PricelistUploadService
{

    /**
     * @var PricelistUploadCollectionFactory
     */
    protected $pricelistUploadCollectionFactory;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    public function __construct(
        PricelistUploadCollectionFactory $pricelistUploadCollectionFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->pricelistUploadCollectionFactory = $pricelistUploadCollectionFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * get pricelist upload by supplier id
     * @param $supplierId
     * @return \Magestore\DropshipSuccess\Model\ResourceModel\Supplier\PricelistUpload\Collection
     */
    public function getPricelistUploadBySupplierId($supplierId)
    {
        /** @var \Magestore\DropshipSuccess\Model\ResourceModel\Supplier\PricelistUpload\Collection $pricelistUploadCollection */
        $pricelistUploadCollection = $this->pricelistUploadCollectionFactory->create();
        $pricelistUploadCollection->addFieldToFilter(SupplierPricelistUploadInterface::SUPPLIER_ID, $supplierId);
        $pricelistUploadCollection->setOrder(SupplierPricelistUploadInterface::SUPPLIER_PRICELIST_UPLOAD_ID, 'DESC');

        return $pricelistUploadCollection;
    }

    /**
     * dropship path to upload pricelist
     * @param $supplierId
     * @return string
     */
    public function getDropshipUploadPath($supplierId)
    {
        return 'dropship/pricelist/'.$supplierId;
    }

    /**
     * @param SupplierPricelistUploadInterface $pricelistUpload
     * @return string
     */
    public function  getPricelistUploadLink(SupplierPricelistUploadInterface $pricelistUpload)
    {
        $supplierId = $pricelistUpload->getSupplierId();
        $pricelistUploadLink = $this->getDropshipUploadPath($supplierId) . '/'.$pricelistUpload->getFileUpload();
        return $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA). $pricelistUploadLink;
    }

    /**
     * @param $supplierId
     * @param $fileName
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getPriceListLinkBySupplierAndUpload($supplierId, $fileUpload)
    {
        $pricelistUploadLink = $this->getDropshipUploadPath($supplierId) . '/'.$fileUpload;
        return $pricelistUploadLink;
    }

}