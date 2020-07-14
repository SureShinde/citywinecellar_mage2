<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Ui\Component\Listing\Columns\PickRequest\Renderer;

class OrderIncrementId extends \Magestore\FulfilSuccess\Ui\Component\Listing\Columns\Actions
{

    protected $_editUrl = 'sales/order/view';
    
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
                    $title = $item[$name];
                    $item[$name] = array();
                    $item[$name]['edit'] = [
                        'href' => $this->urlBuilder->getUrl($this->_editUrl, ['order_id' => $item[$indexField]]),
                        'label' => __($title)
                    ];
                }
            }
        }

        return $dataSource;
    }    
}
