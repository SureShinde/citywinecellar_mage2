<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\SupplierSuccess\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class AbstractSource
 * @package Magestore\SupplierSuccess\Model\Source
 */
abstract class AbstractSource implements OptionSourceInterface
{

    /**
     * @var \Magestore\SupplierSuccess\Service\Supplier\PricingListService
     */
    protected $pricingListService;

    /**
     * @var \Magestore\SupplierSuccess\Service\SupplierService
     */
    protected $supplierService;

    /**
     * AbstractSource constructor.
     * @param \Magestore\SupplierSuccess\Service\Supplier\PricingListService $pricingListService
     */
    public function __construct(
        \Magestore\SupplierSuccess\Service\Supplier\PricingListService $pricingListService,
        \Magestore\SupplierSuccess\Service\SupplierService $supplierService
    ) {
        $this->pricingListService = $pricingListService;
        $this->supplierService = $supplierService;
    }

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [];
    }
}
