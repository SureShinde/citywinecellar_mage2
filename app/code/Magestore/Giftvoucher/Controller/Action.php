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
 * @category Magestore
 * @package  Magestore_Giftvoucher
 * @module   Giftvoucher
 * @author   Magestore Developer
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
     *
     */
    public function execute()
    {
    }

    /**
     * @return mixed
     */
    public function getResultRawFactory()
    {
        return $this->_objectManager->create('Magento\Framework\Controller\Result\RawFactory');
    }

    /**
     * @return mixed
     */
    public function getResultJsonFactory()
    {
        return $this->_objectManager->create('Magento\Framework\Controller\Result\JsonFactory')->create();
    }

    /**
     * @return mixed
     */
    public function getResultJson()
    {
        return $this->_objectManager->create('Magento\Framework\Controller\Result\Json');
    }

    /**
     * @return mixed
     */
    public function getForwardFactory()
    {
        return $this->_objectManager->create('Magento\Framework\Controller\Result\Forward');
    }

    /**
     * @return mixed
     */
    public function getRedirectFactory()
    {
        return $this->_objectManager->create('Magento\Framework\Controller\Result\Redirect');
    }

    /**
     * @return mixed
     */
    public function getPageFactory()
    {
        return $this->_objectManager->create('Magento\Framework\View\Result\PageFactory')->create();
    }

    /**
     * @return mixed
     */
    public function getLayoutFactory()
    {
        return $this->_objectManager->create('Magento\Framework\View\Result\LayoutFactory')->create();
    }

    /**
     * @return mixed
     */
    public function getCusomterSessionModel()
    {
        return $this->_objectManager->get('Magento\Customer\Model\Session');
    }

    /**
     * @return mixed
     */
    public function getHttpContextObj()
    {
        return $this->_objectManager->create('Magento\Framework\App\Http\Context');
    }

    /**
     *
     * @return \Magestore\Giftvoucher\Helper\Data
     */
    public function getHelperData()
    {
        return $this->_objectManager->create('Magestore\Giftvoucher\Helper\Data');
    }

    /**
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
                ->setPath('customer/account/login', array('_secure' => true));
            return $resultRedirectFactory;
        }
    }

    /**
     * @return mixed
     */
    public function customerLoggedIn()
    {
        return $this->getCusomterSessionModel()->isLoggedIn();
    }

    /**
     * @return mixed
     */
    public function getCustomer()
    {
        return $this->getCusomterSessionModel()->getCustomer();
    }

    /**
     * @param $modelName
     * @return mixed
     */
    public function getModel($modelName)
    {
        return $this->_objectManager->create($modelName);
    }

    /**
     * @param $modelName
     * @return mixed
     */
    public function getSingleton($modelName)
    {
        return $this->_objectManager->get($modelName);
    }

    /**
     * @return mixed
     */
    public function getHelper()
    {
        return $this->_objectManager->create('Magestore\Giftvoucher\Helper\Data');
    }

    /**
     * @return mixed
     */
    public function getGiftvoucherModel()
    {
        return $this->_objectManager->create('Magestore\Giftvoucher\Model\Giftvoucher');
    }

    /**
     * @return mixed
     */
    public function getFileSystem()
    {
        return $this->_objectManager->create('\Magento\Framework\Filesystem');
    }
}
