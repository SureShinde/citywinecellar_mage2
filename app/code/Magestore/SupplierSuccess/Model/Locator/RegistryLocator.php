<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\SupplierSuccess\Model\Locator;

use Magestore\SupplierSuccess\Api\Data\SupplierInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\Registry;
use Magento\Backend\Model\Session;

/**
 * Class RegistryLocator
 */
class RegistryLocator implements LocatorInterface
{
    /**
     * @var Registry
     */
    private $registry;

    /**
     * @var SupplierInterface
     */
    private $supplier;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @param Registry $registry
     */
    public function __construct(
        Registry $registry,
        Session $session
    ) {
        $this->registry = $registry;
        $this->session = $session;
    }

    /**
     * {@inheritdoc}
     * @throws NotFoundException
     */
    public function getSupplier()
    {
        if (null !== $this->supplier) {
            return $this->supplier;
        }

        if ($supplier = $this->registry->registry(\Magestore\SupplierSuccess\Api\Data\SupplierInterface::CURRENT_SUPPLIER)) {
            return $this->supplier = $supplier;
        }
    }

    /**
     * {@inheritdoc}
     * @throws NotFoundException
     */
    public function getSupplierProduct()
    {

    }

    /**
     * {@inheritdoc}
     */
    public function getSupplierPricingList()
    {
    }

    /**
     * @param string
     * @return mixed
     */
    public function getSession($key) {
        return $this->session->getData($key, null);
    }

    /**
     * @param string string
     * @return
     */
    public function setSession($key, $data)
    {
        $this->session->setData($key, $data);
    }

    /**
     * @param string string
     * @return
     */
    public function unsetSession($key)
    {
        $this->setSession($key, null);
    }

}
