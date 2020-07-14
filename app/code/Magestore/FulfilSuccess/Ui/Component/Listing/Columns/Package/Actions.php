<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Ui\Component\Listing\Columns\Package;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Shipping\Helper\Data as ShippingHelper;
use Magento\Sales\Model\Order\Shipment\TrackFactory;
use Magento\Sales\Model\ResourceModel\Order\Shipment\Track as TrackResource;


class Actions extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var ShippingHelper
     */
    protected $helper;

    /**
     * @var TrackFactory
     */
    protected $trackFactory;

    /**
     * @var TrackResource
     */
    protected $trackResource;

    /**
     * Actions constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param ShippingHelper $helper
     * @param TrackFactory $trackFactory
     * @param TrackResource $trackResource
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        ShippingHelper $helper,
        TrackFactory $trackFactory,
        TrackResource $trackResource,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->helper = $helper;
        $this->trackFactory = $trackFactory;
        $this->trackResource = $trackResource;
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
            $indexField = $this->getData('config/indexField');
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                if (isset($item[$indexField])) {
                    $item[$name]['edit'] = [
                        'href' => '',
                        'url' => $this->getTrackUrl($item[$indexField]),
                        'label' => $this->getTrackById($item[$indexField])->getTrackNumber()
                    ];
                }
            }
        }

        return $dataSource;
    }

    /**
     * @param $trackId
     * @return string
     */
    public function getTrackById($trackId)
    {
        $track = $this->trackFactory->create();
        $this->trackResource->load($track, $trackId);
        return $track;
    }

    /**
     * @param $trackId
     * @return string
     */
    public function getTrackUrl($trackId)
    {
        $track = $this->getTrackById($trackId);
        $url = $this->helper->getTrackingPopupUrlBySalesModel($track);
        return $url;
    }
}
