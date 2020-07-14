<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\SupplierSuccess\Model;

use Magento\Backend\Model\Auth\StorageInterface;
use Magento\Framework\Session\Config\ConfigInterface;
use Magento\Framework\Session\SaveHandlerInterface;
use Magento\Framework\Session\SidResolverInterface;
use Magento\Framework\Session\ValidatorInterface;
use Magestore\SupplierSuccess\Api\SupplierRepositoryInterface;
use Magestore\SupplierSuccess\Model\SupplierFactory;

/**
 * Customer session model
 * @method string getNoReferer()
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Session extends \Magento\Framework\Session\SessionManager
{

    protected $isCustomerIdChecked;

    /**
     * @var \Magento\Framework\App\Http\Context
     */
    protected $httpContext;

    /**
     * Supplier model
     *
     * @var Supplier
     */
    protected $supplierModel;

    /**
     * @var SupplierRepositoryInterface
     */
    protected $supplierRepository;

    /**
     * @var \Magestore\SupplierSuccess\Model\SupplierFactory
     */
    protected $supplierFactory;

    /**
     * @var ResourceModel\Supplier\CollectionFactory
     */
    protected $supplierCollectionFactory;

    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Framework\Session\SidResolverInterface $sidResolver,
        \Magento\Framework\Session\Config\ConfigInterface $sessionConfig,
        \Magento\Framework\Session\SaveHandlerInterface $saveHandler,
        \Magento\Framework\Session\ValidatorInterface $validator,
        \Magento\Framework\Session\StorageInterface $storage,
        \Magento\Framework\Stdlib\CookieManagerInterface $cookieManager,
        \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory $cookieMetadataFactory,
        \Magento\Framework\App\State $appState,
        SupplierRepositoryInterface $supplierRepository,
        \Magento\Framework\App\Http\Context $httpContext,
        SupplierFactory $supplierFactory,
        \Magestore\SupplierSuccess\Model\ResourceModel\Supplier\CollectionFactory $supplierCollectionFactory
    ) {
        $this->supplierRepository = $supplierRepository;
        $this->httpContext = $httpContext;
        $this->supplierFactory = $supplierFactory;
        $this->supplierCollectionFactory = $supplierCollectionFactory;
        parent::__construct(
            $request,
            $sidResolver,
            $sessionConfig,
            $saveHandler,
            $validator,
            $storage,
            $cookieManager,
            $cookieMetadataFactory,
            $appState
        );
    }

    /**
     * Checking customer login status
     *
     * @api
     * @return bool
     */
    public function isLoggedIn()
    {
        return (bool)$this->getSupplierId()
        && (bool)$this->checkSupplierId($this->getId());
    }

    /**
     * Retrieve supplier id from current session
     *
     * @return int|null
     */
    public function getId()
    {
        return $this->getSupplierId();
    }

    /**
     * Set supplier id
     *
     * @param int|null $supplierId
     * @return $this
     */
    public function setId($supplierId)
    {
        return $this->setSupplierId($supplierId);
    }

    /**
     * Set supplier id
     *
     * @param int|null $id
     * @return $this
     */
    public function setSupplierId($id)
    {
        $this->storage->setData('supplier_id', $id);
        return $this;
    }

    /**
     * Retrieve supplier id from current session
     *
     * @api
     * @return int|null
     */
    public function getSupplierId()
    {
        if ($this->storage->getData('supplier_id')) {
            return $this->storage->getData('supplier_id');
        }
        return null;
    }

    /**
     * Set supplier
     *
     * @param object $supplier
     * @return $this
     */
    public function setSupplier($supplier)
    {
        $this->storage->setData('supplier', $supplier);
        $this->supplierModel = $supplier;
        return $this;
    }

    /**
     * Retrieve supplier model object
     *
     * @return Supplier
     * use getSupplierId() instead
     */
    public function getSupplier()
    {
        if ($this->supplierModel === null) {
            $this->supplierModel = $this->supplierFactory->create()->load($this->getSupplierId());
        }
        return $this->supplierModel;
    }

    /**
     * Check exists supplier (light check)
     *
     * @param int $supplierId
     * @return bool
     */
    public function checkSupplierId($supplierId)
    {
        if ($this->isCustomerIdChecked === $supplierId) {
            return true;
        }

        try {
            $this->supplierRepository->getById($supplierId);
            $this->isCustomerIdChecked = $supplierId;
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * @param $username
     * @param $password
     * @return bool
     */
    public function login($username, $password)
    {
        if ($supplier = $this->authenticate($username, $password)) {
            $this->setSupplierAsLoggedIn($supplier);
            return true;
        }
        return false;
    }

    /**
     * @param $supplier
     * @return $this
     */
    public function setSupplierAsLoggedIn($supplier)
    {
        $this->setSupplier($supplier);
        $this->setSupplierId($supplier->getId());
        return $this;
    }

    /**
     * @param $username
     * @param $password
     * @return null
     */
    public function authenticate($username, $password)
    {
        /** @var \Magestore\SupplierSuccess\Model\Supplier $supplier */
        $supplier = $this->supplierCollectionFactory->create()
            ->addFieldToFilter(
                ['supplier_name', 'supplier_code', 'contact_email', 'telephone'],
                [
                    ['eq' => $username],
                    ['eq' => $username],
                    ['eq' => $username]
                ]
            )
            ->setPageSize(1)
            ->setCurPage(1)
            ->getFirstItem();
        if ($supplier->getId() && md5($password) == $supplier->getPassword()){
            return $supplier;
        }else{
            return null;
        }
    }
}
