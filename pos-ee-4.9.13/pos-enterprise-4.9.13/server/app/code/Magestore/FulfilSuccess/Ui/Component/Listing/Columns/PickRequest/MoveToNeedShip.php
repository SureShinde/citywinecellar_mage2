<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\FulfilSuccess\Ui\Component\Listing\Columns\PickRequest;


class MoveToNeedShip extends \Magestore\FulfilSuccess\Ui\Component\Listing\Columns\Actions
{
    protected $_editUrl = 'fulfilsuccess/pickRequest/moveNeedToShip';
    
    /**
     * @var string
     */
    protected $actionLabel = 'Move';    
    

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
                        'href' => $this->urlBuilder->getUrl($this->_editUrl, ['id' => $item[$indexField]]),
                        'label' => __($this->actionLabel),
                        'target' => '_blank',
                    ];
                }
            }
        }

        return $dataSource;
    }
    
}
