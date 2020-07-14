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
 * @package     Magestore_Storepickup
 * @copyright   Copyright (c) 2012 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 */

namespace Magestore\Storepickup\Plugin\AdminOrder;

use Magento\Framework\Exception\InputException;

/**
 * Class Create
 * @package Magestore\Storepickup\Plugin\AdminOrder
 */
class Create
{

    /**
     * @var \Magento\Framework\App\Request\Http
     */
    protected $request;

    /**
     * @var \Magestore\Storepickup\Model\StoreFactory
     */
    protected $storeFactory;

    /**
     * @var \Magestore\Storepickup\Helper\Region
     */
    protected $regionHelper;
    /**
     * @var \Magento\Directory\Model\RegionFactory
     */
    protected $regionFactory;
    /**
     * @var \Magento\Backend\Model\Session\Quote
     */
    protected $backendQuoteSession;
    /**
     * @var \Magento\Quote\Model\QuoteRepository
     */
    protected $quoteRepository;
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;

    /**
     * Create constructor.
     * @param \Magento\Framework\App\Request\Http $request
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     * @param \Magestore\Storepickup\Model\StoreFactory $storeFactory
     * @param \Magestore\Storepickup\Helper\Region $regionHelper
     */
    public function __construct(
        \Magento\Framework\App\Request\Http $request,
        \Magento\Directory\Model\RegionFactory $regionFactory,
        \Magestore\Storepickup\Model\StoreFactory $storeFactory,
        \Magento\Backend\Model\Session\Quote $backendQuoteSession,
        \Magento\Quote\Model\QuoteRepository $quoteRepository,
        \Psr\Log\LoggerInterface $logger,
        \Magestore\Storepickup\Helper\Region $regionHelper
    )
    {
        $this->request = $request;
        $this->storeFactory = $storeFactory;
        $this->regionHelper = $regionHelper;
        $this->regionFactory = $regionFactory;
        $this->backendQuoteSession = $backendQuoteSession;
        $this->quoteRepository = $quoteRepository;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Sales\Controller\Adminhtml\Order\Create $subject
     * @throws InputException
     */
    public function beforeExecute(\Magento\Sales\Controller\Adminhtml\Order\Create\Save $subject)
    {
        $storePickupId = $this->request->getParam('store_pickup_id');
        if ($storePickupId) {
            $dataShipping = [];
            $store = $this->storeFactory->create()->load($storePickupId, 'storepickup_id');
            $dataShipping['customer_address_id'] = '';
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
            try {
                $this->quoteRepository->save($quote);
            } catch (\Exception $e) {
                $this->logger->critical($e);
                throw new InputException(__('Unable to save shipping information. Please check input data.'));
            }
        }
    }
}
