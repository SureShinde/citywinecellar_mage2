<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Ui\Component\Location\Form\General;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Form\Field;

/**
 * Class StockId
 * @package Magestore\Webpos\Ui\Component\Location\Form\General
 */
class StockId extends Field
{
    /**
     * @var \Magestore\Webpos\Api\WebposManagementInterface
     */
    private $webposManagement;

    /**
     * @var \Magento\Framework\ObjectManagerInterface
     */
    private $objectManager;

    /**
     * StockId constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magestore\Webpos\Api\WebposManagementInterface $webposManagement
     * @param \Magento\Framework\ObjectManagerInterface $objectManager
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magestore\Webpos\Api\WebposManagementInterface $webposManagement,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        array $components = [],
        array $data = []
    )
    {
        $this->webposManagement = $webposManagement;
        $this->objectManager = $objectManager;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Add js listener to reset button
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     * @since 100.2.0
     */
    public function prepare()
    {
        parent::prepare();

        $isMSIEnabled = $this->webposManagement->isMSIEnable();
        $config = $this->getData('config');

        if (!$isMSIEnabled) {
            $config['componentDisabled'] = true;
        } else {
            if ($this->webposManagement->isWebposStandard()) {
                $config['disabled'] = true;
                /** @var \Magento\InventoryCatalogApi\Api\DefaultStockProviderInterface $defaultStockProvider */
                $defaultStockProvider = $this->objectManager->create('Magento\InventoryCatalogApi\Api\DefaultStockProviderInterface');
                $config['default'] = $defaultStockProvider->getId();
            }
        }
        $this->setData('config', $config);
    }
}
