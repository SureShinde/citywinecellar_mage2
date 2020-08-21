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

namespace Magestore\Storepickup\Controller\Checkout;

use Magento\Framework\App\Action\HttpPostActionInterface;

/**
 * Class ChangeStore
 *
 * Used to change store
 */
class ChangeStore extends \Magento\Framework\App\Action\Action implements HttpPostActionInterface
{
    /**
     * @var \Magento\Framework\Controller\Result\JsonFactory
     */
    protected $_resultJsonFactory;

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
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * ChangeStore constructor.
     *
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magestore\Storepickup\Model\StoreFactory $storeFactory
     * @param \Magestore\Storepickup\Helper\Region $regionHelper
     * @param \Magento\Directory\Model\RegionFactory $regionFactory
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\Controller\Result\JsonFactory $resultJsonFactory,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magestore\Storepickup\Model\StoreFactory $storeFactory,
        \Magestore\Storepickup\Helper\Region $regionHelper,
        \Magento\Directory\Model\RegionFactory $regionFactory
    ) {
        parent::__construct($context);
        $this->_resultJsonFactory = $resultJsonFactory;
        $this->_checkoutSession = $checkoutSession;
        $this->storeFactory = $storeFactory;
        $this->regionHelper = $regionHelper;
        $this->regionFactory = $regionFactory;
    }

    /**
     * Execute
     *
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $storepickup_session = ['store_id' => $this->getRequest()->getParam('store_id')];
        $this->_checkoutSession->setData('storepickup_session', $storepickup_session);

        $store = $this->storeFactory->create()
            ->load($this->getRequest()->getParam('store_id'), 'storepickup_id');

        $dataShipping['firstname'] = __('Store');
        $dataShipping['lastname'] = $store->getData('store_name');

        if ($store->getData('address')) {
            $dataShipping['street'] = $store->getData('address');
        } else {
            $dataShipping['street'] = __('N/A');
        }

        $dataShipping['city'] = $store->getData('city');

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
            $dataShipping['fax'] = __('N/A');
        }

        if ($store->getPhone()) {
            $dataShipping['telephone'] = $store->getPhone();
        } else {
            $dataShipping['telephone'] = __('N/A');
        }

        $storepickup_session['dataShipping'] = $dataShipping;

        return $this->getResponse()->setBody(\Zend_Json::encode($storepickup_session));
    }
}
