<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Model\Source\DropshipRequest;

use Magento\Framework\Data\OptionSourceInterface;
use Magestore\DropshipSuccess\Model\Source\AbstractSource;
/**
 * Class IsActive
 */
class Supplier extends AbstractSource implements OptionSourceInterface
{

    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        /** @var \Magestore\SupplierSuccess\Api\SupplierRepositoryInterface $supplierRepository */
        $supplierRepository = \Magento\Framework\App\ObjectManager::getInstance()->create(
            '\Magestore\SupplierSuccess\Api\SupplierRepositoryInterface'
        );
        $supplierCollection = $supplierRepository->getAllSupplier();
        $availableOptions = [];
//        $availableOptions[0] = __('Non supplier');
        /** @var \Magestore\SupplierSuccess\Model\Supplier $supplier */
        foreach ($supplierCollection as $supplier) {
            $availableOptions[$supplier->getId()] = $supplier->getSupplierName();
        }
        $options = [];
        foreach ($availableOptions as $key => $value) {
            $options[] = [
                'label' => $value,
                'value' => $key,
            ];
        }
        return $options;
    }
}
