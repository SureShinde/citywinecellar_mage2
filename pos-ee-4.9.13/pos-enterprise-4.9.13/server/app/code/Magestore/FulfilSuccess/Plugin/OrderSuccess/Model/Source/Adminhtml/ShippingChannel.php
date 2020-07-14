<?php
/**
 * Copyright © 2018 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\FulfilSuccess\Plugin\OrderSuccess\Model\Source\Adminhtml;


class ShippingChannel
{
    /**
     * @var \Magestore\FulfilSuccess\Api\FulfilManagementInterface
     */
    protected $fulfilManagement;

    /**
     * ShippingChannel constructor.
     * @param \Magestore\FulfilSuccess\Api\FulfilManagementInterface $fulfilManagement
     */
    public function __construct(
        \Magestore\FulfilSuccess\Api\FulfilManagementInterface $fulfilManagement
    )
    {
        $this->fulfilManagement = $fulfilManagement;
    }

    /**
     * @param \Magestore\OrderSuccess\Model\Source\Adminhtml\ShippingChanel $subject
     * @param $result
     * @return mixed
     */
    public function afterGetShippingChanels($subject, $result)
    {
        try {
            if ($this->fulfilManagement->isMSIEnable()) {
                $shippingChannels = $result->getShippingChanels();
                if (!empty($shippingChannels)) {
                    foreach ($shippingChannels as &$shippingChannel) {
                        if ($shippingChannel['code'] == 'fulfil') {
                            $shippingChannel['title'] = __('Request Pick from Source');
                        }
                    }
                }
                $result->setShippingChanels($shippingChannels);
            }
            return $result;
        } catch (\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}