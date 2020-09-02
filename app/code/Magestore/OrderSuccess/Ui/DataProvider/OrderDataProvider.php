<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\OrderSuccess\Ui\DataProvider;

/**
 * Class OrderDataProvider
 * @package Magestore\OrderSuccess\Ui\DataProvider
 */
class OrderDataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
    /**
     * context
     *
     * @var \Magestore\OrderSuccess\Ui\DataProvider\Context
     */
    protected $context;

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * ListDataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param \Magestore\OrderSuccess\Ui\DataProvider\Context $context
     * @param \Magento\Framework\Registry $coreRegistry
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        \Magestore\OrderSuccess\Ui\DataProvider\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        array $meta = [],
        array $data = []
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        $this->context = $context;
        $this->coreRegistry = $coreRegistry;
        $this->initCollection();
    }

    /**
     * {@inheritdoc}
     */
    public function initCollection()
    {
        $collection = $this->getOrderCollection();
        $this->collection = $collection;
    }

    /**
     * Returns search criteria
     *
     *  {@inheritdoc}
     */
    public function getSearchCriteria(){
        return $this->getCollection();
    }

    /**
     *
     */
    public function getOrderCollection()
    {
        return;
    }

}
