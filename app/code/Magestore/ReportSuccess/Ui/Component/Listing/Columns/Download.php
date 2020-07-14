<?php
/**
 *  Copyright © 2018 Magestore. All rights reserved.
 *  See COPYING.txt for license details.
 */

namespace Magestore\ReportSuccess\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

/**
 * Class Download
 * @package Magestore\ReportSuccess\Ui\Component\Listing\Columns
 */
class Download extends Column
{
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
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
        $this->urlBuilder = $urlBuilder;
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
        $url7zip = __(
            'The archive can be uncompressed with <a href="%1">%2</a> on Windows systems.',
            'http://www.7-zip.org/',
            '7-Zip'
        );

        if(isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');

            foreach ($dataSource['data']['items'] as & $item) {

                $item[$fieldName] = '<a href="' . $this->urlBuilder->getUrl(
                        'omcreports/inventory/download',
                        ['name' => $item['name'], 'display_name' => $item['display_name'], 'date_object' => date('YmdHis', strtotime($item['date_object']))]
                    ) . '">' . $item[
                        'extension'
                    ] . '</a> &nbsp; <small>(' . $url7zip . ')</small>';
            }
        }
        return $dataSource;
    }
}
