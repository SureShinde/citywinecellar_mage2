<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\BarcodeSuccess\Block\Adminhtml\Barcode\Edit\Button;

use Magestore\BarcodeSuccess\Api\Data\BarcodeInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\UiComponent\Context;
use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Magento\Framework\App\Response\RedirectInterface;

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
     * Registry
     *
     * @var Registry
     */
    protected $registry;

    /**
     * @var RedirectInterface
     */
    protected $redirect;

    /**
     * Generic constructor
     *
     * @param Context $context
     * @param Registry $registry
     */
    public function __construct(
        Context $context,
        Registry $registry,
        RedirectInterface $redirect
    ) {
        $this->context = $context;
        $this->registry = $registry;
        $this->redirect = $redirect;
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
    public function getBarcode()
    {
        return $this->registry->registry('current_barcode');
    }

    /**
     * {@inheritdoc}
     */
    public function getButtonData()
    {
        return [];
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        if ($this->redirect->getRefererUrl()) {
            return $this->redirect->getRefererUrl();
        }
        return $this->getUrl('*/*/');
    }
}
