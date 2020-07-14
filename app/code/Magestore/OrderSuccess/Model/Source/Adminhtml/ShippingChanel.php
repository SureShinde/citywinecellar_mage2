<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\OrderSuccess\Model\Source\Adminhtml;

use Magestore\OrderSuccess\Api\Data\ShippingChanelInterface;

/**
 * Class ShippingChanel
 * @package Magestore\OrderSuccess\Model\Source\Adminhtml
 */
class ShippingChanel implements \Magestore\OrderSuccess\Api\Data\ShippingChanelInterface
{

    /**
     * @var array
     */
    protected $chanels;

    /**
     * 
     * @param array $chanels
     */
    public function __construct( array $chanels)
    {
        $this->chanels = $chanels;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        return [
            ['label' => __('Create Shipment'), 'value' => ShippingChanelInterface::SHIP],
            ['label' => __('Back Sales'), 'value' => ShippingChanelInterface::BACKORDER],
            ['label' => __('Request Pick Items'), 'value' => ShippingChanelInterface::FULFIL],
            ['label' => __('Dropship'), 'value' => ShippingChanelInterface::DROPSHIP],
        ];
    }
     

    /**
     * @return array
     */
    public function getShippingChanels()
    {
        /*
        $chanels = [
            [
                'code'=> ShippingChanelInterface::BACKORDER,
                'title' => __('Back Sales'),
                'block'=> 'Magestore\OrderSuccess\Block\Adminhtml\Sales\BackOrder\Grid'
            ],
            [
                'code'=> ShippingChanelInterface::FULFIL,
                'title' => __('Request Pick Items'),
                'block'=> 'Magestore\OrderSuccess\Block\Adminhtml\Sales\ShipProcess\Grid'
            ],
            [
                'code'=> ShippingChanelInterface::DROPSHIP,
                'title' => __('Dropship'),
                'block'=> 'Magestore\OrderSuccess\Block\Adminhtml\Sales\Dropship\Grid'
            ]
        ];
        $data = new \Magento\Framework\DataObject(array('shipping_chanels' => $chanels));
        $this->eventManager->dispatch('ordersuccess_shipping_chanels', ['data' => $data]);
        */
        $data = new \Magento\Framework\DataObject(array('shipping_chanels' => $this->chanels));
         
        return $data;
    }
    
    
    /**
     * @return array
     */
    public function getOptionArray()
    {
        $chanels = $this->getShippingChanels()->getData('shipping_chanels');
        $options = [];
        foreach($chanels as $chanel){
            $options[$chanel['code']] = $chanel['title'];
        }
        return $options;
    }

    /**
     * @return array
     */
    public function getOptionBlockArray()
    {
        $chanels = $this->getShippingChanels()->getData('shipping_chanels');
        $options = [];
        foreach($chanels as $chanel){
            $options[$chanel['code']] = $chanel['block'];
        }
        return $options;
    }

}