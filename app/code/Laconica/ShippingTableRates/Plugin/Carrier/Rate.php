<?php

namespace Laconica\ShippingTableRates\Plugin\Carrier;

use Magento\Quote\Api\Data\ShippingMethodExtensionFactory;

class Rate
{
    /**
     * @var ShippingMethodExtensionFactory
     */
    protected $extensionFactory;

    /**
     * Description constructor.
     * @param ShippingMethodExtensionFactory $extensionFactory
     */
    public function __construct(ShippingMethodExtensionFactory $extensionFactory)
    {
        $this->extensionFactory = $extensionFactory;
    }

    /**
     * @param $subject
     * @param $result
     * @param $rateModel
     * @return mixed
     */
    public function afterModelToDataObject($subject, $result, $rateModel)
    {
        $extensionAttribute = $result->getExtensionAttributes() ? $result->getExtensionAttributes() : $this->extensionFactory->create();
        $extensionAttribute->setIsTips($rateModel->getIsTips());
        $result->setExtensionAttributes($extensionAttribute);
        return $result;
    }
}
