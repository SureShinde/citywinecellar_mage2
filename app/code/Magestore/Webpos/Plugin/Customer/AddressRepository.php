<?php

namespace Magestore\Webpos\Plugin\Customer;

class AddressRepository {
    public function beforeSave(
        \Magento\Customer\Model\ResourceModel\AddressRepository $subject,
        \Magento\Customer\Api\Data\AddressInterface $address
    ) {
        if($address->getCustomAttributes()) {
            foreach ($address->getCustomAttributes() as $customAttribute) {
                if($customAttribute->getAttributeCode() != 'sub_id') {
                    continue;
                }
                $subId = $customAttribute->getValue();

                $currentData = $this->getCustomerAddressBySubId($subId);
                if ($currentData) {
                    $address->setId($currentData);
                }
            }
        }
    }

    /**
     * @param int $subId
     * @return int|bool
     */
    public function getCustomerAddressBySubId($subId){
        $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
        /** @var \Magento\Customer\Model\ResourceModel\Address\Collection $addressCollection */
        $addressCollection = $objectManager->create('Magento\Customer\Model\ResourceModel\Address\Collection');
        $addressCollection->addAttributeToFilter('sub_id', $subId);
        if($addressCollection->getSize()) {
            return $addressCollection->getFirstItem()->getId();
        }
        return false;
    }
}