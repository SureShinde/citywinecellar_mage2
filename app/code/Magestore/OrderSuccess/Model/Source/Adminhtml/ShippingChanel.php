<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magestore\OrderSuccess\Model\Source\Adminhtml;

use Magestore\OrderSuccess\Api\Data\ShippingChanelInterface;

/**
 * Source ShippingChanel
 */
class ShippingChanel implements \Magestore\OrderSuccess\Api\Data\ShippingChanelInterface
{
    /**
     * @var array
     */
    protected $chanels;

    /**
     * ShippingChanel constructor.
     *
     * @param array $chanels
     */
    public function __construct(array $chanels)
    {
        $this->chanels = $chanels;
    }

    /**
     * To Option Array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return [
//            ['label' => __('Create Shipment'), 'value' => ShippingChanelInterface::SHIP],
            ['label' => __('Back Sales'), 'value' => ShippingChanelInterface::BACKORDER],
            ['label' => __('Request Pick Items'), 'value' => ShippingChanelInterface::FULFIL],
            ['label' => __('Dropship'), 'value' => ShippingChanelInterface::DROPSHIP],
        ];
    }

    /**
     * Get Shipping Chanels
     *
     * @return array
     */
    public function getShippingChanels()
    {
        $data = new \Magento\Framework\DataObject(['shipping_chanels' => $this->chanels]);
        return $data;
    }

    /**
     * Get Option Array
     *
     * @return array
     */
    public function getOptionArray()
    {
        $chanels = $this->getShippingChanels()->getData('shipping_chanels');
        $options = [];
        foreach ($chanels as $chanel) {
            $options[$chanel['code']] = $chanel['title'];
        }
        return $options;
    }

    /**
     * Get Option Block Array
     *
     * @return array
     */
    public function getOptionBlockArray()
    {
        $chanels = $this->getShippingChanels()->getData('shipping_chanels');
        $options = [];
        foreach ($chanels as $chanel) {
            $options[$chanel['code']] = $chanel['block'];
        }
        return $options;
    }
}
