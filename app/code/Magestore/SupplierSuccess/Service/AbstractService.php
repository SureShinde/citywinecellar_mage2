<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Service;

use Magento\Directory\Model\CountryFactory;
use Magento\Directory\Model\RegionFactory;

abstract class AbstractService
{

    /**
     * @var \Magestore\SupplierSuccess\Model\ResourceModel\SupplierFactory
     */
    protected $_supplierResourceFactory;

    /**
     * @var \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\ProductFactory
     */
    protected $_supplierProductResourceFactory;

    /**
     * @var \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\PricingListFactory
     */
    protected $supplierPricingListResourceFactory;

    /**
     * @var \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\CollectionFactory
     */
    protected $supplierCollectionFactory;

    /**
     * @var \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Product\CollectionFactory
     */
    protected $_supplierProductCollectionFactory;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    protected $objectManager;

    /**
     * CSV Processor
     *
     * @var \Magento\Framework\File\Csv
     */
    protected $csvProcessor;

    /**
     * @var \Magestore\SupplierSuccess\Api\SupplierRepositoryInterface
     */
    protected $supplierRepositoryInterface;

    /**
     * @var \Magento\Framework\Math\Random
     */
    protected $random;

    /**
     * @var RegionFactory
     */
    protected $regionFactory;

    /**
     * @var CountryFactory
     */
    protected $countryFactory;

    /**
     * @var \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\PricingList\CollectionFactory
     */
    protected $supplierPricingListCollectionFactory;

    /**
     * @var EmailService
     */
    protected $emailService;

    public function __construct(
        \Magestore\SupplierSuccess\Model\ResourceModel\SupplierFactory $supplierResourceFactory,
        \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\CollectionFactory $supplierCollectionFactory,
        \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\ProductFactory $supplierProductResourceFactory,
        \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\PricingListFactory $supplierPricingListResourceFactory,
        \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\Product\CollectionFactory $supplierProductCollectionFactory,
        \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\PricingList\CollectionFactory $supplierPricingListCollectionFactory,
        \Magento\Framework\File\Csv $csvProcessor,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magestore\SupplierSuccess\Api\SupplierRepositoryInterface $supplierRepositoryInterface,
        \Magento\Framework\Math\Random $random,
        RegionFactory $regionFactory,
        CountryFactory $countryFactory,
        EmailService $emailService
    ) {
        $this->_supplierResourceFactory = $supplierResourceFactory;
        $this->_supplierProductResourceFactory = $supplierProductResourceFactory;
        $this->supplierPricingListResourceFactory = $supplierPricingListResourceFactory;
        $this->_supplierProductCollectionFactory = $supplierProductCollectionFactory;
        $this->supplierCollectionFactory = $supplierCollectionFactory;
        $this->csvProcessor = $csvProcessor;
        $this->objectManager = $objectManager;
        $this->supplierRepositoryInterface = $supplierRepositoryInterface;
        $this->random = $random;
        $this->regionFactory = $regionFactory;
        $this->countryFactory = $countryFactory;
        $this->supplierPricingListCollectionFactory = $supplierPricingListCollectionFactory;
        $this->emailService = $emailService;
    }
}