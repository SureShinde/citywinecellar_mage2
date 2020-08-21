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
 * @package     Magestore_Customercredit
 * @copyright   Copyright (c) 2017 Magestore (http://www.magestore.com/)
 * @license     http://www.magestore.com/license-agreement.html
 *
 */

namespace Magestore\Customercredit\Block\Sharecredit;

use Magento\Framework\Pricing\PriceCurrencyInterface;
use Zend\Uri\Uri;

/**
 * Class Grid
 *
 * Share credit grid block
 */
class Grid extends \Magento\Framework\View\Element\Template
{

    protected $_columns = [];

    /**
     * Grid's Collection
     *
     * @var \Magestore\Customercredit\Model\ResourceModel\Creditcode\Collection
     */
    protected $_collection;

    /**
     * @var PriceCurrencyInterface
     */
    protected $_priceCurrency;

    /**
     * @var Uri
     */
    protected $uri;

    /**
     * @var \Magento\Customer\Model\Customer
     */
    protected $_customer;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customersession;

    /**
     * @var \Magento\Framework\Url\DecoderInterface
     */
    protected $urlDecoder;

    /**
     * Grid constructor.
     *
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param \Magento\Framework\Url\DecoderInterface $decode
     * @param PriceCurrencyInterface $priceCurrency
     * @param Uri $uri
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Magento\Customer\Model\Session $customersession
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Url\DecoderInterface $decode,
        PriceCurrencyInterface $priceCurrency,
        Uri $uri,
        \Magento\Customer\Model\Customer $customer,
        \Magento\Customer\Model\Session $customersession,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->_priceCurrency = $priceCurrency;
        $this->uri = $uri;
        $this->_customer = $customer;
        $this->_customersession = $customersession;
        $this->urlDecoder = $decode;
    }

    /**
     * Get Price Currency
     *
     * @return PriceCurrencyInterface
     */
    public function getPriceCurrency()
    {
        return $this->_priceCurrency;
    }

    /**
     * Get Customer
     *
     * @return \Magento\Customer\Model\Customer
     */
    public function getCustomer()
    {
        $customer_id = $this->_customersession->getId();
        return $this->_customer->load($customer_id);
    }

    /**
     * Get Columns
     *
     * @return array
     */
    public function getColumns()
    {
        return $this->_columns;
    }

    /**
     * Set Collection
     *
     * @param \Magestore\Customercredit\Model\ResourceModel\Creditcode\Collection $collection
     * @return $this
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function setCollection($collection)
    {
        $this->_collection = $collection;
        if (!$this->getData('add_searchable_row')) {
            return $this;
        }
        foreach ($this->getColumns() as $columnId => $column) {
            if (isset($column['searchable']) && $column['searchable']) {
                if (isset($column['filter_function']) && $column['filter_function']) {
                    $this->fetchFilter($column['filter_function']);
                } else {
                    $field = isset($column['index']) ? $column['index'] : $columnId;
                    $field = isset($column['filter_index']) ? $column['filter_index'] : $field;
                    if ($filterValue = $this->getFilterValue($columnId)) {
                        $this->_collection->addFieldToFilter($field, ['like' => "%$filterValue%"]);
                    }
                    if ($filterValue = $this->getFilterValue($columnId, '-from')) {
                        if ($column['type'] == 'price') {
                            $store = $this->_storeManager->getStore();
                            $filterValue /= $store->getBaseCurrency()->convert(1, $store->getCurrentCurrency());
                        } elseif ($column['type'] == 'date' || $column['type'] == 'datetime') {
                            $filterValue = date('Y-m-d', strtotime($filterValue));
                        }
                        $this->_collection->addFieldToFilter($field, ['gteq' => $filterValue]);
                    }
                    $filterValue = $this->getFilterValue($columnId, '-to');
                    if ($filterValue || $this->getFilterValue($columnId, '-to') == '0') {
                        if ($column['type'] == 'price') {
                            $store = $this->_storeManager->getStore();
                            $filterValue /= $store->getBaseCurrency()->convert(1, $store->getCurrentCurrency());
                        } elseif ($column['type'] == 'date' || $column['type'] == 'datetime') {
                            $filterValue = date('Y-m-d', strtotime($filterValue) + 86400);
                        }
                        $this->_collection->addFieldToFilter($field, ['lteq' => $filterValue]);
                    }
                }
            }
        }

        return $this;
    }

    /**
     * Get Filter Value
     *
     * @param null|string $columnId
     * @param string $offset
     * @return mixed
     */
    public function getFilterValue($columnId = null, $offset = '')
    {
        if (!$this->hasData('filter_value')) {
            if ($filter = $this->getRequest()->getParam('filter')) {
                $filter = $this->urlDecoder->decode($filter);
                $this->uri->setQuery($filter);
                $filter = $this->uri->getQueryAsArray();
            }
            $this->setData('filter_value', $filter);
        }
        if ($columnId === null) {
            return $this->getData('filter_value');
        } else {
            return $this->getData('filter_value/' . $columnId . $offset);
        }
    }

    /**
     * Fetch Filter
     *
     * @param mixed $parentFuction
     * @return mixed
     */
    public function fetchFilter($parentFuction)
    {
        $parentBlock = $this->getParentBlock();
        return $parentBlock->$parentFuction($this->_collection, $this->getFilterValue());
    }

    /**
     * Get Filter Url
     *
     * @return mixed
     */
    public function getFilterUrl()
    {
        if (!$this->hasData('filter_url')) {
            $this->setData('filter_url', $this->getUrl('*/*/*'));
        }
        return $this->getData('filter_url');
    }

    /**
     * Get Pager Html
     *
     * @return string
     */
    public function getPagerHtml()
    {
        if ($this->getData('add_searchable_row')) {
            return $this->getParentBlock()->getPagerHtml();
        }
        return '';
    }

    /**
     * Get Collection
     *
     * @return \Magestore\Customercredit\Model\ResourceModel\Creditcode\Collection
     */
    public function getCollection()
    {
        return $this->_collection;
    }

    /**
     * @inheritDoc
     */
    public function _prepareLayout()
    {
        parent::_prepareLayout();
        $this->setTemplate('customercredit/share/grid.phtml');
        return $this;
    }

    /**
     * Add Column
     *
     * @param string $columnId
     * @param array $params
     * @return $this
     */
    public function addColumn($columnId, $params)
    {
        if (isset($params['searchable']) && $params['searchable']) {
            $this->setData('add_searchable_row', true);
            if (isset($params['type']) && ($params['type'] == 'date' || $params['type'] == 'datetime')) {
                $this->setData('add_calendar_js_to_grid', true);
            }
        }
        $this->_columns[$columnId] = $params;
        return $this;
    }

    /**
     * Fetch Render
     *
     * @param mixed $parentFunction
     * @param \Magento\Framework\DataObject $row
     * @return mixed
     */
    public function fetchRender($parentFunction, $row)
    {
        $parentBlock = $this->getParentBlock();

        $fetchObj = new \Magento\Framework\DataObject(
            [
                'function' => $parentFunction,
                'html' => false,
            ]
        );

        if ($fetchObj->getHtml()) {
            return $fetchObj->getHtml();
        }
        return $parentBlock->$parentFunction($row);
    }
}
