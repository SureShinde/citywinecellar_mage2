<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\BarcodeSuccess\Block\Adminhtml\Template\Edit\Button;

use Magestore\BarcodeSuccess\Api\Data\BarcodeInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\UiComponent\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magestore\BarcodeSuccess\Model\Locator\LocatorInterface;

/**
 * Class Generic
 */
class Generic implements ButtonProviderInterface
{
    /**
     * Url Builder
     *
     * @var Context
     */
    protected $context;

    /**
     * LocatorInterface
     *
     * @var $locator
     */
    protected $locator;

    /**
     * Generic constructor
     *
     * @param Context $context
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        LocatorInterface $locator
    ) {
        $this->context = $context;
        $this->locator = $locator;
    }

    /**
     * Generate url by route and parameters
     *
     * @param string $route
     * @param array $params
     * @return string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrl($route, $params);
    }

    /**
     * Get product
     *
     * @return BarcodeInterface
     */
    public function getTemplate()
    {
        return $this->locator->get('current_barcode_template');
    }

    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        return [];
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getParam($key){
        return $this->context->getRequestParam($key,false);
    }

    /**
     * @return mixed
     */
    public function getParams(){
        return $this->context->getRequestParams();
    }
}
