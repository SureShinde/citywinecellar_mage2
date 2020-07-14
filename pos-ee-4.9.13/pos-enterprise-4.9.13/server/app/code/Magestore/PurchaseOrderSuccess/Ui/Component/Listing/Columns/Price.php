<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\PurchaseOrderSuccess\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\Pricing\PriceCurrencyInterface;

/**
 * Class Price
 * @package Magestore\PurchaseOrderSuccess\Ui\Component\Listing\Columns
 */
class Price extends Column
{
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceCurrency;

    /**
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    protected $currencyFactory;

    /**
     * @var []
     */
    protected $currency;

    /**
     * Price constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param PriceCurrencyInterface $priceFormatter
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        PriceCurrencyInterface $priceCurrency,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        array $components = [],
        array $data = []
    ) {
        $this->priceCurrency = $priceCurrency;
        $this->currencyFactory = $currencyFactory;
        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $currencyRate = $this->context->getRequestParam('currency_rate', 1);
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                if(isset($item['currency_rate']))
                    $currencyRate = $item['currency_rate'];
                $price = $this->priceCurrency->convert($item[$this->getData('name')] * $currencyRate);
                $currencyCode = isset($item['currency_code']) ? $item['currency_code'] : 'default';
                if($currencyCode == 'default'){
                    $currencyCode = $this->context->getRequestParam('currency_code', 'default');
                }
                if($currencyCode != 'default' && !isset($this->currency[$currencyCode]))
                    $this->currency[$currencyCode] = $this->currencyFactory->create()->load($currencyCode);
                else if(!isset($this->currency[$currencyCode]))
                    $this->currency['default'] = $this->currencyFactory->create()->load(null);
                $item[$this->getData('name')] = $this->currency[$currencyCode]->formatTxt($price);
//                $this->priceFormatter->convertAndFormat(
//                    $item[$this->getData('name')],
//                    false,
//                    null,
//                    null,
//                    $currencyCode
//                );
            }
        }
        return $dataSource;
    }
}
