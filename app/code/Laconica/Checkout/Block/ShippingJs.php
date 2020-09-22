<?php

namespace Laconica\Checkout\Block;

use Laconica\Checkout\Helper\StateConfig;
use Magento\Framework\View\Element\Template;

class ShippingJs extends Template
{
    /**
     * @var StateConfig $stateConfigHelper
     */
    private $stateConfigHelper;

    public function __construct(
        Template\Context $context,
        StateConfig $stateConfigHelper,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->stateConfigHelper = $stateConfigHelper;
    }

    /**
     * Returns configs for js validation
     *
     * @return false|string
     */
    public function getConfigs()
    {
        $data = [
            'enabled' => intval($this->stateConfigHelper->isEnabled()),
            'error_message' => $this->stateConfigHelper->getJsMessageText()
        ];
        return json_encode($data);
    }
}