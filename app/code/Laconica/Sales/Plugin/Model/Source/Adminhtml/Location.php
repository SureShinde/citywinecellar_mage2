<?php

namespace Laconica\Sales\Plugin\Model\Source\Adminhtml;

class Location
{
    public function afterToOptionArray(
        \Magestore\Webpos\Model\Source\Adminhtml\Location $subject,
        array $result
    ) {
        array_push($result, [
            'label' => 'Web stores',
            'value' => 0
        ]);
        return $result;
    }
}