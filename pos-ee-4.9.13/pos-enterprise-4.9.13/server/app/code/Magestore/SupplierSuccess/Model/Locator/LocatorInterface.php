<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\SupplierSuccess\Model\Locator;

use Magestore\SupplierSuccess\Api\Data\SupplierInterface;
use Magestore\SupplierSuccess\Api\Data\SupplierProductInterface;
use Magestore\SupplierSuccess\Api\Data\SupplierPricingListInterface;

/**
 * Interface LocatorInterface
 */
interface LocatorInterface
{
    /**
     * @return SupplierInterface
     */
    public function getSupplier();

    /**
     * @return SupplierProductInterface
     */
    public function getSupplierProduct();

    /**
     * @return SupplierPricingListInterface
     */
    public function getSupplierPricingList();

    /**
     * @param string
     * @return mixed
     */
    public function getSession($key);

    /**
     * @param string string
     * @return
     */
    public function setSession($key, $data);

    /**
     * @param string string
     * @return
     */
    public function unsetSession($key);
}
