<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\AdjustStock\Ui\Component\Listing\Columns\Renderer;

/**
 * Class Source
 *
 * Source column renderer
 */
class Source extends \Magestore\AdjustStock\Ui\Component\Listing\Columns\Actions
{
    /**
     * @var string
     */
    protected $_editUrl = 'inventory/source/edit';

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $labelField = $this->getData('config/labelField');
            foreach ($dataSource['data']['items'] as &$item) {
                $name = $this->getData('name');
                if (isset($item[$labelField])) {
                    $sourceCode = $item[$name];
                    $item[$name] =[];
                    $item[$name]['edit'] = [
                        'href' => $this->urlBuilder->getUrl($this->_editUrl, ['source_code' => $sourceCode]),
                        'label' => __($item[$labelField])
                    ];
                }
            }
        }

        return $dataSource;
    }
}
