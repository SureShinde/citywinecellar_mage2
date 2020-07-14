<?php

namespace Magestore\Webpos\Ui\Component\Listing\Column\Store;

class Options extends \Magento\Store\Ui\Component\Listing\Column\Store\Options {
    public function toOptionArray()
    {
        $nullData = [
            [
                'label' => __('--Please Select--'),
                'value' => null
            ]
        ];

        return array_merge($nullData, parent::toOptionArray());
    }
}