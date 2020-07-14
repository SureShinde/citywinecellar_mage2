<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\OrderSuccess\Ui\Component\Listing\Columns;

use Magestore\OrderSuccess\Api\Data\BatchInterfaceFactory;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Class FilterAction
 * @package Magestore\OrderSuccess\Ui\Component\Listing\Columns
 */
class FilterAction extends \Magento\Ui\Component\Listing\Columns\Column
{

    /**
     * @var BatchInterfaceFactory 
     */
    protected $batchFactory;


    /**
     * FilterAction constructor.
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param BatchInterfaceFactory $batchFactory
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        BatchInterfaceFactory $batchFactory,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
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
                if (isset($item[$indexField]) && $item[$indexField] != 0) {
                    $batch->setId($item[$indexField]);
                    $item[$name] = array();
                    $item[$name]['edit'] = [
                        'label' => $batch->getCode(),
                        'itemid' => $batch->getId(),
                        'href' => '',
                    ];
                }
            }
        }
        return $dataSource;
    }    
}
