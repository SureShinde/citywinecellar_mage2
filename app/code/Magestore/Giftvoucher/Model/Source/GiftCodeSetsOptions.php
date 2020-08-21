<?php

/**
 * Copyright © 2017 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\Giftvoucher\Model\Source;

/**
 * Class GiftCodeSetsOptions
 *
 * Source - gift code sets options model
 */
class GiftCodeSetsOptions extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource
{
    /**
     * @var \Magestore\Giftvoucher\Model\GiftCodeSets|GiftCodeSets
     */
    protected $_giftcodeSets;

    /**
     * GiftCodeSetsOptions constructor.
     *
     * @param \Magestore\Giftvoucher\Model\GiftCodeSets|GiftCodeSets $giftcodeSets
     */
    public function __construct(
        \Magestore\Giftvoucher\Model\GiftCodeSets $giftcodeSets
    ) {
        $this->_giftcodeSets = $giftcodeSets;
    }

    /**
     * Get Available Giftcode Sets
     *
     * @return array
     */
    public function getAvailableGiftcodeSets()
    {
        $giftcodeSets = $this->_giftcodeSets->getCollection();
        $listGiftcodeSets = [];
        foreach ($giftcodeSets as $giftcodeSet) {
            $listGiftcodeSets[] = [
                'label' => $giftcodeSet->getSetName(),
                'value' => $giftcodeSet->getSetId()
            ];
        }
        return  $listGiftcodeSets;
    }

    /**
     * Get All Options
     *
     * @param bool $withEmpty
     * @return array
     */
    public function getAllOptions($withEmpty = true)
    {
        if ($this->_options === null) {
            $this->_options = $this->getAvailableGiftcodeSets();
        }
        $options = $this->_options;
        if ($withEmpty) {
            array_unshift($options, [
                'value' => '',
                'label' => __('-- Please Select --'),
            ]);
        }
        return $options;
    }
}
