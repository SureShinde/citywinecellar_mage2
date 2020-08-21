<?php
/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Giftvoucher\Controller;

use Magento\Customer\Model\Session;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Giftvoucher Action
 *
 * @module   Giftvoucher
 * @author   Magestore Developer
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 * @SuppressWarnings(PHPMD.AllPurposeAction)
 */
class Action extends \Magento\Framework\App\Action\Action
{

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @param \Magento\Backend\Block\Template\Context|\Magento\Framework\App\Action\Context $context
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param PriceCurrencyInterface $priceCurrency
     * @internal param array $data
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        PriceCurrencyInterface $priceCurrency
    ) {
        parent::__construct($context);
        $this->_storeManager = $storeManager;
        $this->resultPageFactory = $resultPageFactory;
        $this->priceCurrency = $priceCurrency;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        return null;
    }

    /**
     * Get Result Raw Factory
     *
     * @return \Magento\Framework\Controller\Result\RawFactory
     */
    public function getResultRawFactory()
    {
        return $this->_objectManager->create(\Magento\Framework\Controller\Result\RawFactory::class);
    }

    /**
     * Get Result Json Factory
     *
     * @return \Magento\Framework\Controller\Result\JsonFactory
     */
    public function getResultJsonFactory()
    {
        return $this->_objectManager->create(\Magento\Framework\Controller\Result\JsonFactory::class)->create();
    }

    /**
     * Get Result Json
     *
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function getResultJson()
    {
        return $this->_objectManager->create(\Magento\Framework\Controller\Result\Json::class);
    }

    /**
     * Get Forward Factory
     *
     * @return \Magento\Framework\Controller\Result\Forward
     */
    public function getForwardFactory()
    {
        return $this->_objectManager->create(\Magento\Framework\Controller\Result\Forward::class);
    }

    /**
     * Get Redirect Factory
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function getRedirectFactory()
    {
        return $this->_objectManager->create(\Magento\Framework\Controller\Result\Redirect::class);
    }

    /**
     * Get Page Factory
     *
     * @return \Magento\Framework\View\Result\PageFactory
     */
    public function getPageFactory()
    {
        return $this->_objectManager->create(\Magento\Framework\View\Result\PageFactory::class)->create();
    }

    /**
     * Get Layout Factory
     *
     * @return \Magento\Framework\View\Result\LayoutFactory
     */
    public function getLayoutFactory()
    {
        return $this->_objectManager->create(\Magento\Framework\View\Result\LayoutFactory::class)->create();
    }

    /**
     * Get Cusomter Session Model
     *
     * @return \Magento\Customer\Model\Session
     */
    public function getCusomterSessionModel()
    {
        return $this->_objectManager->get(\Magento\Customer\Model\Session::class);
    }

    /**
     * Get Http Context Obj
     *
     * @return \Magento\Framework\App\Http\Context
     */
    public function getHttpContextObj()
    {
        return $this->_objectManager->create(\Magento\Framework\App\Http\Context::class);
    }

    /**
     * Get Helper Data
     *
     * @return \Magestore\Giftvoucher\Helper\Data
     */
    public function getHelperData()
    {
        return $this->_objectManager->create(\Magestore\Giftvoucher\Helper\Data::class);
    }

    /**
     * Init Function
     *
     * @param string $title
     * @return mixed
     */
    public function initFunction($title = '')
    {
        if ($this->customerLoggedIn()) {
            $resultPageFactory = $this->getPageFactory();
            $resultPageFactory->getConfig()->getTitle()->set($title);
            return $resultPageFactory;
        } else {
            $resultRedirectFactory = $this->getRedirectFactory()
                ->setPath('customer/account/login', ['_secure' => true]);
            return $resultRedirectFactory;
        }
    }

    /**
     * Customer Logged In
     *
     * @return mixed
     */
    public function customerLoggedIn()
    {
        return $this->getCusomterSessionModel()->isLoggedIn();
    }

    /**
     * Get Customer
     *
     * @return mixed
     */
    public function getCustomer()
    {
        return $this->getCusomterSessionModel()->getCustomer();
    }

    /**
     * Get Model
     *
     * @param string $modelName
     * @return mixed
     */
    public function getModel($modelName)
    {
        return $this->_objectManager->create($modelName);
    }

    /**
     * Get Singleton
     *
     * @param string $modelName
     * @return mixed
     */
    public function getSingleton($modelName)
    {
        return $this->_objectManager->get($modelName);
    }

    /**
     * Get Helper
     *
     * @return \Magestore\Giftvoucher\Helper\Data
     */
    public function getHelper()
    {
        return $this->_objectManager->create(\Magestore\Giftvoucher\Helper\Data::class);
    }

    /**
     * Get Giftvoucher Model
     *
     * @return \Magestore\Giftvoucher\Model\Giftvoucher
     */
    public function getGiftvoucherModel()
    {
        return $this->_objectManager->create(\Magestore\Giftvoucher\Model\Giftvoucher::class);
    }

    /**
     * Get File System
     *
     * @return \Magento\Framework\Filesystem
     */
    public function getFileSystem()
    {
        return $this->_objectManager->create(\Magento\Framework\Filesystem::class);
    }
}
