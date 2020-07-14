<?php
/**
 * Magestore
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magestore.com license that is
 * available through the world-wide-web at this URL:
 * http://www.magestore.com/license-agreement.html
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Magestore
 * @package     Magestore_Giftvoucher
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

namespace Magestore\Storepickup\Controller\Adminhtml\Checkout;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\View\Result\PageFactory;

class ChangeStore extends \Magento\Framework\App\Action\Action
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $_backendSession;
    /**
     * @var PageFactory
     */
    protected $resultPageFactory;
    /**
     * @var \Magento\Backend\Model\Session\Quote
     */
    protected $backendQuoteSession;
    /**
     * @var \Magestore\Storepickup\Model\StoreFactory
     */
    protected $storeFactory;
    /**
     * @var \Magento\Quote\Model\QuoteRepository
     */
    protected $quoteRepository;
    /**
     * @var \Magestore\Storepickup\Helper\Region
     */
    protected $regionHelper;
    /**
     * @var \Magento\Directory\Model\RegionFactory
     */
    protected $regionFactory;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * ChangeStore constructor.
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Backend\Model\Session $backendSession
     * @param \Magento\Backend\Model\Session\Quote $backendQuoteSession
     * @param \Magestore\Storepickup\Model\StoreFactory $storeFactory
     * @param \Magento\Quote\Model\QuoteRepository $quoteRepository
     * @param \Magestore\Storepickup\Helper\Region $regionHelper
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param PageFactory $resultPageFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Backend\Model\Session $backendSession,
        \Magento\Backend\Model\Session\Quote $backendQuoteSession,
        \Magestore\Storepickup\Model\StoreFactory $storeFactory,
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        \Magestore\Storepickup\Helper\Region $regionHelper,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Psr\Log\LoggerInterface $logger,
        PageFactory $resultPageFactory
    )
    {
        parent::__construct($context);
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_backendSession = $backendSession;
        $this->resultPageFactory = $resultPageFactory;
        $this->backendQuoteSession = $backendQuoteSession;
        $this->storeFactory = $storeFactory;
        $this->quoteRepository = $quoteRepository;
        $this->regionHelper = $regionHelper;
        $this->regionFactory = $regionFactory;
        $this->logger = $logger;
    }
    public function execute()
    {
        $result = [];
        $storepickup_session = array('store_id' => $this->getRequest()->getParam('store_id'));
        $this->_backendSession->setData('storepickup',$storepickup_session);
        $this->updateTax($this->getRequest()->getParam('store_id'));

        /** @var \Magento\Framework\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->addHandle('sales_order_create_load_block_totals');
        $result['totals'] = $resultPage->getLayout()->renderElement('content');

        return $this->getResponse()->setBody(\Zend_Json::encode($result));
    }

    /**
     * @param $storeId
     */
    public function updateTax($storeId)
    {
        $dataShipping = [];
        $store = $this->storeFactory->create()->load($storeId, 'storepickup_id');
        $dataShipping['firstname'] = __('Store');
        $dataShipping['lastname'] = $store->getData('store_name');
        $dataShipping['street'][0] = $store->getData('address');
        $dataShipping['city'] = $store->getCity();

        if ($store->getStateId()) {
            /** @var \Magento\Directory\Model\Region $region */
            $region = $this->regionFactory->create()->load($store->getStateId());
            $dataShipping['region'] = $region->getName();
            $dataShipping['region_id'] = $store->getStateId();
        } else {
            $dataShipping['region'] = $store->getData('state');
            $dataShipping['region_id'] = 0;
        }

        $dataShipping['postcode'] = $store->getData('zipcode');
        $dataShipping['country_id'] = $store->getData('country_id');
        $dataShipping['company'] = '';
        if ($store->getFax()) {
            $dataShipping['fax'] = $store->getFax();
        } else {
            unset($dataShipping['fax']);
        }

        if ($store->getPhone()) {
            $dataShipping['telephone'] = $store->getPhone();
        } else {
            unset($dataShipping['telephone']);
        }

        $dataShipping['save_in_address_book'] = 0;
        $quote = $this->backendQuoteSession->getQuote();
        $quote->getShippingAddress()->addData($dataShipping);
    }
}
