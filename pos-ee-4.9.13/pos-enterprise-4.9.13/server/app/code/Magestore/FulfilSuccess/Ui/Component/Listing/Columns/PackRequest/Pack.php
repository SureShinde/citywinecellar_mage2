<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Ui\Component\Listing\Columns\PackRequest;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;

class Pack extends Column
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var \Magestore\FulfilSuccess\Api\FulfilManagementInterface
     */
    protected $fulfilManagement;

    /**
     * Pack constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param \Magestore\FulfilSuccess\Api\FulfilManagementInterface $fulfilManagement
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        \Magestore\FulfilSuccess\Api\FulfilManagementInterface $fulfilManagement,
        array $components = [],
        array $data = []
    )
    {
        $this->urlBuilder = $urlBuilder;
        $this->fulfilManagement = $fulfilManagement;
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
            $isMSIEnable = $this->fulfilManagement->isMSIEnable();
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                $item[$fieldName . '_html'] = "<button class='button'><span>Pack</span></button>";
                $item[$fieldName . '_title'] = __('Pack');
                $item[$fieldName . '_pack_request_id'] = $item['pack_request_id'];
                $item[$fieldName . '_warehouse_id'] = $item['warehouse_id'];
                $warehouseId = $isMSIEnable ? $item['source_code'] : $item['warehouse_id'];
                $url = $this->urlBuilder->getUrl('fulfilsuccess/packRequest/pack',
                    [
                        'autoload' => 1,
                        'warehouse_id' => $warehouseId
                    ]
                );
                $item[$fieldName . '_url'] = $url;
            }
        }

        return $dataSource;
    }
}