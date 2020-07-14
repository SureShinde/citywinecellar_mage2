<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Webpos\Model\Source\Adminhtml\Receipt;

use Magento\Framework\Data\ValueSourceInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class StockConfiguration
 */
class ReceiptConfiguration implements ValueSourceInterface
{

    protected $scopeConfig;

    const XML_PATH_ITEM = 'webpos/custom_receipt/';

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * {@inheritdoc}
     */
    public function getValue($name)
    {
        $value= $this->scopeConfig->getValue(
            self::XML_PATH_ITEM . $name,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        return $value;
    }
}
