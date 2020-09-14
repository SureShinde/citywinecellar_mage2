<?php

namespace Laconica\Analytics\Block\Html;

use Magento\Framework\View\Element\Template;

class Gtm extends Template
{
    /**
     * @var \Laconica\Analytics\Helper\Config $configHelper
     */
    protected $configHelper;

    /**
     * Gtm constructor.
     * @param Template\Context $context
     * @param \Laconica\Analytics\Helper\Config $configHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        \Laconica\Analytics\Helper\Config $configHelper,
        array $data = []
    )
    {
        parent::__construct($context, $data);
        $this->configHelper = $configHelper;
    }

    /**
     * @return \Laconica\Analytics\Helper\Config
     */
    public function getConfigHelper()
    {
        return $this->configHelper;
    }
}