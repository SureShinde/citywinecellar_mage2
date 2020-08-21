<?php

/**
 * Copyright © Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\PosReports\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\Pricing\PriceCurrencyInterface;
use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class Price
 *
 * Used to create Price
 */
class Price extends Column
{
    /**
     * @var PriceCurrencyInterface
     */
    protected $priceFormatter;

    /**
     * Price constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param PriceCurrencyInterface $priceFormatter
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        PriceCurrencyInterface $priceFormatter,
        array $components = [],
        array $data = []
    ) {
        $this->priceFormatter = $priceFormatter;
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
        if (isset($dataSource['data']['items']) && !empty($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $currencyCode = isset($item['base_currency_code']) ? $item['base_currency_code'] : null;
                if (isset($item[$this->getData('name')])) {
                    $item[$this->getData('name')] = $this->priceFormatter->format(
                        $item[$this->getData('name')],
                        false,
                        null,
                        null,
                        $currencyCode
                    );
                }
            }
        }

        return $dataSource;
    }
}
