<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\OrderSuccess\Ui\Component\Listing\Columns;

use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;

/**
 * Class FulfilAction
 * @package Magestore\OrderSuccess\Ui\Component\Listing\Columns
 */
class FulfilAction extends \Magento\Ui\Component\Listing\Columns\Column
{
    /**
     * @var string
     */
    protected $actionLabel = 'Fulfill';

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
                        'callback' => '*/*/',
                        'href' => '',
                    ];
                }
            }
        }
        return $dataSource;
    }    
}
