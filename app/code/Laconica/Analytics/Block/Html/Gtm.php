<?php

namespace Laconica\Analytics\Block\Html;

use Magento\Framework\View\Element\Template;

class Gtm extends Template
{
    /**
     * @var \Laconica\Analytics\Helper\Config $configHelper
     */
    protected $configHelper;

    public function __construct(
        Template\Context $context,
        \Laconica\Analytics\Helper\Config $configHelper,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->configHelper = $configHelper;
    }

    public function getConfigHelper()
    {
        return $this->configHelper;
    }
}