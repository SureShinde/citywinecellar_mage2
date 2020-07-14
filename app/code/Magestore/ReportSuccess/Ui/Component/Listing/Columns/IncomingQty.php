<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

/**
 * Class IncomingQty
 * @package Magestore\ReportSuccess\Ui\Component\Listing\Columns
 */
class IncomingQty extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;
    /**
     * @var \Magento\Framework\Module\Manager
     */
    protected $moduleManager;

    protected $objectManager;
    /**
     * @var ContextInterface
     */
    protected $context;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        array $components = [],
        array $data = [],
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\ObjectManagerInterface $objectManager
    )
    {
        $this->urlBuilder = $urlBuilder;
        $this->moduleManager = $moduleManager;
        $this->objectManager = $objectManager;
        $this->context = $context;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare component configuration
     *
     * @return void
     */
    public function prepare()
    {
        parent::prepare();
        if (!$this->moduleManager->isEnabled('Magestore_PurchaseOrderSuccess')){
            $this->_data['config']['componentDisabled'] = true;
        } else {
            parent::prepare();
        }
    }
}