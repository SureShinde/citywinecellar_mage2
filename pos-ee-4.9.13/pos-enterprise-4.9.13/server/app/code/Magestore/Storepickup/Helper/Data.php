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

namespace Magestore\Storepickup\Helper;

/**
 * Helper Data.
 * @category Magestore
 * @package  Magestore_Storepickup
 * @module   Storepickup
 * @author   Magestore Developer
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /**
     * @var \Magento\Backend\Block\Widget\Grid\Column\Renderer\Options\Converter
     */
    protected $_converter;

    /**
     * @var \Magestore\Storepickup\Model\Factory
     */
    protected $_factory;

    /**
     * @var \Magestore\Storepickup\Model\StoreFactory
     */
    protected $_storeFactory;

    /**
     * @var \Magento\Backend\Model\Session
     */
    protected $_backendSession;

    /**
     * @var array
     */
    protected $_sessionData = null;

    /**
     * @var \Magento\Backend\Helper\Js
     */
    protected $_backendHelperJs;

    /**
     * @var \Magestore\Storepickup\Model\ResourceModel\Store\CollectionFactory
     *
     */
    protected $_storeCollectionFactory;

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $_filesystem;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;


    /**
     * Data constructor.
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magestore\Storepickup\Model\Factory $factory
     * @param \Magento\Backend\Block\Widget\Grid\Column\Renderer\Options\Converter $converter
     * @param \Magento\Backend\Helper\Js $backendHelperJs
     * @param \Magento\Framework\Filesystem $filesystem
     * @param \Magento\Backend\Model\Session $backendSession
     * @param \Magestore\Storepickup\Model\ResourceModel\Store\CollectionFactory $storeCollectionFactory
     * @param \Magestore\Storepickup\Model\StoreFactory $storeFactory
     * @param \Magento\Checkout\Model\Session $checkoutSession
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magestore\Storepickup\Model\Factory $factory,
        \Magento\Backend\Block\Widget\Grid\Column\Renderer\Options\Converter $converter,
        \Magento\Backend\Helper\Js $backendHelperJs,
        \Magento\Framework\Filesystem $filesystem,
        \Magento\Backend\Model\Session $backendSession,
        \Magestore\Storepickup\Model\ResourceModel\Store\CollectionFactory $storeCollectionFactory,
        \Magestore\Storepickup\Model\StoreFactory $storeFactory,
        \Magento\Checkout\Model\Session $checkoutSession
    )
    {
        parent::__construct($context);
        $this->_factory = $factory;
        $this->_converter = $converter;
        $this->_backendHelperJs = $backendHelperJs;
        $this->_filesystem = $filesystem;
        $this->_backendSession = $backendSession;
        $this->_storeCollectionFactory = $storeCollectionFactory;
        $this->_storeFactory = $storeFactory;
        $this->_checkoutSession = $checkoutSession;
    }

    public function filterStoreByItemInCart(\Magestore\Storepickup\Model\ResourceModel\Store\Collection $collection)
    {
        if ($quote_id = $this->_checkoutSession->getQuote()->getId()) {
            $collection->getSelect()->joinLeft(['stock_item' => $collection->getTable('cataloginventory_stock_item')],
                'main_table.warehouse_id = stock_item.website_id', ['avaiable_qty' => 'stock_item.qty'])
                ->joinLeft(['quote_item' => $collection->getTable('quote_item')],
                    'quote_item.product_id = stock_item.product_id', ['selected_qty' => 'quote_item.qty'])
                ->group('quote_item.product_id')
                ->group('main_table.storepickup_id')
                ->where('quote_item.quote_id = ' . $quote_id);
        }

    }

    /**
     * get selected stores in serilaze grid store.
     *
     * @return array
     */
    public function getTreeSelectedStores()
    {
        $sessionData = $this->_getSessionData();

        if ($sessionData) {
            return $this->_converter->toTreeArray(
                $this->_backendHelperJs->decodeGridSerializedInput($sessionData)
            );
        }

        $entityType = $this->_getRequest()->getParam('entity_type');
        $id = $this->_getRequest()->getParam('enitity_id');

        /** @var \Magestore\Storepickup\Model\AbstractModelManageStores $model */
        $model = $this->_factory->create($entityType)->load($id);

        return $model->getId() ? $this->_converter->toTreeArray($model->getStorepickupIds()) : [];
    }

    /**
     * get selected rows in serilaze grid of tag, holiday, specialday.
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getTreeSelectedValues()
    {
        $sessionData = $this->_getSessionData();

        if ($sessionData) {
            return $this->_converter->toTreeArray(
                $this->_backendHelperJs->decodeGridSerializedInput($sessionData)
            );
        }

        $storepickupId = $this->_getRequest()->getParam('storepickup_id');
        $methodGetterId = $this->_getRequest()->getParam('method_getter_id');

        /** @var \Magestore\Storepickup\Model\Store $store */
        $store = $this->_storeFactory->create()->load($storepickupId);
        $ids = $store->runGetterMethod($methodGetterId);

        return $store->getId() ? $this->_converter->toTreeArray($ids) : [];
    }

    /**
     * Get session data.
     *
     * @return array
     */
    public function _getSessionData()
    {
        $serializedName = $this->_getRequest()->getParam('serialized_name');
        if ($this->_sessionData === null) {
            $this->_sessionData = $this->_backendSession->getData($serializedName, true);
        }

        return $this->_sessionData;
    }

    public function getDefaultStore()
    {
        return $this->scopeConfig->getValue('carriers/storepickup/default_store', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function isDisplayPickuptime()
    {
        return $this->scopeConfig->getValue('carriers/storepickup/display_pickuptime', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function isAllowSpecificPayments()
    {
        return $this->scopeConfig->getValue('carriers/storepickup/sallowspecific', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getSelectpaymentmethod()
    {
        return $this->scopeConfig->getValue('carriers/storepickup/selectpaymentmethod', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getListStore()
    {
        /** @var \Magestore\Storepickup\Model\ResourceModel\Store\Collection $collection */
        $collection = $this->_storeCollectionFactory->create();
        $collection->addFieldToFilter('status', '1')
            ->addFieldToSelect([
                'storepickup_id',
                'store_name',
                'address',
                'phone',
                'latitude',
                'longitude'
            ]);

        $this->filterStoreByWebsite($collection);
        
        return $collection->getData();
    }

    public function getListStoreJson()
    {
        /** @var \Magestore\Storepickup\Model\ResourceModel\Store\Collection $collection */
        $collection = $this->_storeCollectionFactory->create();
        $collection->addFieldToFilter('status', '1')
            ->addFieldToSelect([
                'storepickup_id',
                'store_name',
                'address',
                'phone',
                'latitude',
                'longitude',
                'city',
                'state',
                'zipcode',
                'country_id',
                'fax'
            ]);

        $this->filterStoreByWebsite($collection);

        return \Zend_Json::encode($collection->getData());
    }

    /**
     * @param \Magestore\Storepickup\Model\ResourceModel\Store\Collection $collection
     * @return \Magestore\Storepickup\Model\ResourceModel\Store\Collection
     */
    public function filterStoreByWebsite($collection)
    {
        if ($this->isMSISourceEnable()) {
            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            /** @var \Magento\InventoryCatalog\Model\GetStockIdForCurrentWebsite $getStockIdForCurrentWebsite */
            $getStockIdForCurrentWebsite = $objectManager->get('Magento\InventoryCatalog\Model\GetStockIdForCurrentWebsite');
            $stockId = $getStockIdForCurrentWebsite->execute();
            /** @var \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder */
            $searchCriteriaBuilder = $objectManager->create('Magento\Framework\Api\SearchCriteriaBuilder');
            $searchCriteria = $searchCriteriaBuilder->addFilter('stock_id', $stockId)->create();
            /** @var \Magento\InventoryApi\Api\GetStockSourceLinksInterface $getStockSourceLinks */
            $getStockSourceLinks = $objectManager->get('Magento\InventoryApi\Api\GetStockSourceLinksInterface');
            $searchResult = $getStockSourceLinks->execute($searchCriteria);
            if ($searchResult->getTotalCount() > 0) {
                $sourceCodes = [];
                foreach ($searchResult->getItems() as $link) {
                    $sourceCodes[] = $link->getSourceCode();
                }
                $collection->addFieldToFilter('source_code', ['in' => $sourceCodes]);
            } else {
                $collection->addFieldToFilter('storepickup_id', 0);
            }

        }
        return $collection;
    }

    public function getChangeTimeURL()
    {
        return $this->getURL('storepickup/checkout/changedate');
    }

    public function generateTimes($mintime, $maxtime, $sys_min_time = '0:0')
    {

        //$sys_min_time = strtotime(date('H:i:s',$sys_min_time));
        $interval_time = $this->scopeConfig->getValue('carriers/storepickup/time_interval', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        $timeHI = explode(':', $mintime);
        $mintime = mktime($timeHI[0], $timeHI[1], 0, '01', '01', '2000');
        $timeHI = explode(':', $maxtime);
        $maxtime = mktime($timeHI[0], $timeHI[1], 0, '01', '01', '2000');
        $timeHI = explode(':', $sys_min_time);
        $sys_min_time = mktime($timeHI[0], $timeHI[1], 0, '01', '01', '2000');
        $listTime = "";

        $i = $mintime;

        while ($i <= $maxtime) {
            if ($i >= $sys_min_time) {
                $time = date('H:i', $i);
                $listTime .= '<option value="' . $time . '">' . $time . '</option>';
                //$listTime[$time] = $time;
            }

            $i += $interval_time * 60;
        }

        return $listTime;
    }

    public function getResponseBody($url)
    {
        if (ini_get('allow_url_fopen') != 1) {
            @ini_set('allow_url_fopen', '1');
        }

        if (ini_get('allow_url_fopen') != 1) {
            $ch = curl_init();
            if (preg_match('/^https:/i', $url)) {
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
            }
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_FAILONERROR, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3);
            $contents = curl_exec($ch);
            curl_close($ch);
        } else {
            $contents = @file_get_contents($url);
        }

        return $contents;
    }

    public function getGoogleApiKey()
    {
        return $this->scopeConfig->getValue('storepickup/service/google_api_key', \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
    }

    public function getBaseDirMedia()
    {
        return $this->_filesystem->getDirectoryRead('media');
    }

    public function getSpecialCountry()
    {
        return strtolower($this->scopeConfig->getValue('storepickup/service/country_suggest_specificcountry', \Magento\Store\Model\ScopeInterface::SCOPE_STORE));
    }

    public function isMSISourceEnable()
    {
        return $this->_moduleManager->isEnabled('Magento_InventoryAdminUi');
    }
}
