<?php

/**
 * Copyright © 2019 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 *
 */

namespace Magestore\Webpos\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

/**
 * Class \Magestore\Webpos\Ui\Component\Listing\Column\UnconvertedOrderActions
 */
class UnconvertedOrderActions extends Column
{
    /**
     * @var UrlInterface
     */
    protected $_urlBuilder;

    /**
     * UnconvertedOrderActions constructor.
     *
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
        array $data = []
    ) {
        $this->_urlBuilder = $urlBuilder;
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
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as & $item) {
                $name = $this->getData('name');
                if (isset($item['increment_id'])) {
                    $item[$name]['convert'] = [
                        'href' => $this->_urlBuilder->getUrl(
                            'webposadmin/unconverted/convert',
                            ['increment_id' => $item['increment_id']]
                        ),
                        'label' => __('Convert'),
                        'confirm' => [
                            'title' => __('Convert'),
                            'message' => __('Are you sure you want to converted selected items?')
                        ]
                    ];
                    $item[$name]['view'] = [
                        'href' => $this->_urlBuilder->getUrl(
                            'webposadmin/unconverted/view',
                            ['id' => $item['id']]
                        ),
                        'label' => __('View')
                    ];
                }
            }
        }
        return $dataSource;
    }
}
