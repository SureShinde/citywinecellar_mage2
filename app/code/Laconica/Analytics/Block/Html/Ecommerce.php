<?php

namespace Laconica\Analytics\Block\Html;

use Magento\Framework\View\Element\Template;

class Ecommerce extends Gtm
{
    public function __construct(
        Template\Context $context,
        \Laconica\Analytics\Helper\Config $configHelper,
        array $data = [])
    {
        parent::__construct($context, $configHelper, $data);
    }

    public function getPushJson(){
        $data = [
            'ecommerce' => [
                'currencyCode' => $this->_storeManager->getStore()->getCurrentCurrency()->getCode()
            ]
        ];

        return json_encode($data);
    }

}