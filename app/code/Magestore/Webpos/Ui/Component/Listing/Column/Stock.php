<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\Webpos\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

/**
 * Class FulfilOnline
 * @package Magestore\Webpos\Ui\Component\Listing\Column
 */
class Stock extends Column
{
    /**
     * @var \Magestore\Webpos\Api\WebposManagementInterface
     */
    protected $webposManagement;

    /**
     * Stock constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param \Magestore\Webpos\Api\WebposManagementInterface $webposManagement
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        \Magestore\Webpos\Api\WebposManagementInterface $webposManagement,
        array $components = [],
        array $data = []
    )
    {
        $this->webposManagement = $webposManagement;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Produce and return block's html output
     *
     * @return string
     */
    public function toHtml()
    {
        $this->render();
    }

    /**
     * {@inheritdoc}
     */
    public function prepare()
    {
        parent::prepare();

        $isMSIEnable = $this->webposManagement->isMSIEnable();

        if (!$isMSIEnable) {
            $config = $this->getData('config');
            $config['componentDisabled'] = true;
            $this->setData('config', $config);
        }
    }
}