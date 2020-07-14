<?php

/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Payment\Block\Payment\Method;

/**
 * Class Cod
 * @package Magestore\Payment\Block\Payment\Method
 */
class Cod extends \Magento\Payment\Block\Info
{
    

    /**
     * Cash constructor.
     * @param \Magento\Framework\View\Element\Template\Context $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {

        parent::__construct(
            $context,
            $data
        );
    }
    /**
     * Get method title from setting
     */
    public function getMethodTitle()
    {
        $title = $this->_scopeConfig->getValue('payment/codforpos/title', ScopeInterface::SCOPE_STORE);
        if ($title == '') {
            $title = __("Cash On Delivery");
        }
        return $title;
    }


}