<?php

namespace Magestore\Customization\Plugin\BarcodeSuccess\Model\Source;

class Attributes
{
    /**
     * @param \Magestore\Webpos\Model\Customer\CustomerRepository $subject
     * @param $customer
     * @return mixed
     */
    public function afterToOptionArray(
        \Magestore\BarcodeSuccess\Model\Source\Attributes $subject,
        $options
    ) {
        $options[] = ['value' => 'price', 'label' => 'Price'];

        return $options;
    }
}
