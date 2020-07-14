<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Ui\Component\Listing\Columns\PackRequest;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;

/**
 * Class CreatePackagesAction
 * @package Magestore\FulfillSuccess\Ui\Component\Listing\Columns\PackRequest
 */
class CreatePackagesAction extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var string
     */
    protected $actionLabel = 'Create Packages';

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * CreatePackagesAction constructor.
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
        array $components,
        array $data = [])
    {
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
        if (isset($dataSource['data']['items'])) {
            $indexField = $this->getData('config/indexField');
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                if (isset($item[$indexField]) && $item[$indexField] != 0) {
                    $item[$name] = array();
                    $item[$name]['edit'] = [
                        'label' => $this->actionLabel,
                        'itemid' => $item[$indexField],
                        'callback' => $this->urlBuilder->getUrl('fulfilsuccess/packRequest/getPackaging', $params = array()),
                        'href' => '',
                    ];
                }
            }
        }
        return $dataSource;
    }
}
