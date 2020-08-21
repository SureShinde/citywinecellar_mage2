<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Ui\Component\Listing\Columns\PickRequest\Renderer;

use Magestore\FulfilSuccess\Api\Data\BatchInterfaceFactory;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Pick renderer BatchNumber
 */
class BatchNumber extends \Magestore\FulfilSuccess\Ui\Component\Listing\Columns\Actions
{
    /**
     * @var string
     */
    protected $_editUrl = 'fulfilsuccess/pickrequest/filterBatch';

    /**
     * @var BatchInterfaceFactory
     */
    protected $batchFactory;

    /**
     * BatchNumber constructor.
     *
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param BatchInterfaceFactory $batchFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        BatchInterfaceFactory $batchFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $urlBuilder, $components, $data);
        $this->batchFactory = $batchFactory;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        $batch = $this->batchFactory->create();

        if (isset($dataSource['data']['items'])) {
            $indexField = $this->getData('config/indexField');
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                if (isset($item[$indexField])) {
                    $batch->setId($item[$indexField]);
                    $item[$name] = [];
                    $item[$name]['edit'] = [
                        'label' => $batch->getCode(),
                        'itemid' => $batch->getId(),
                    ];
                }
            }
        }
        return $dataSource;
    }
}
