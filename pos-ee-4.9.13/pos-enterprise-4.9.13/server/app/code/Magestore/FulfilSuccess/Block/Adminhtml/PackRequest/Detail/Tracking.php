<?php

/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\FulfilSuccess\Block\Adminhtml\PackRequest\Detail;

use Magestore\FulfilSuccess\Api\Data\PackRequestInterface;
use Magestore\FulfilSuccess\Service\PackRequest\PackRequestService;

/**
 * Shipment tracking control form
 *
 */
class Tracking extends \Magento\Shipping\Block\Adminhtml\Order\Tracking
{
    /**
     * @var \Magento\Shipping\Model\CarrierFactory
     */
    protected $_carrierFactory;

    /**
     * @var PackRequestService
     */
    protected $packRequestService;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Shipping\Model\Config $shippingConfig
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Shipping\Model\CarrierFactory $carrierFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Shipping\Model\Config $shippingConfig,
        \Magento\Framework\Registry $registry,
        \Magento\Shipping\Model\CarrierFactory $carrierFactory,
        PackRequestService $packRequestService,
        array $data = []
    ) {
        parent::__construct($context, $shippingConfig, $registry, $data);
        $this->_carrierFactory = $carrierFactory;
        $this->packRequestService = $packRequestService;
    }


    /**
     * @param string $code
     * @return \Magento\Framework\Phrase|string|bool
     */
    public function getCarrierTitle($code)
    {
        $carrier = $this->_carrierFactory->create($code);
        if ($carrier) {
            return $carrier->getConfigData('title');
        } else {
            return __('Custom Value');
        }
        return false;
    }

    /**
     * @return array
     */
    public function _getCarriersInstances()
    {
        return $this->_shippingConfig->getAllCarriers();
    }

    /**
     * @return bool
     */
    public function isPackedRequest()
    {
        $packRequest = $this->_coreRegistry->registry('current_pack_request');
        if ($packRequest && $packRequest->getId()) {
            if (in_array($packRequest->getData(PackRequestInterface::STATUS),
                [PackRequestInterface::STATUS_PACKED, PackRequestInterface::STATUS_CANCELED])) {
                return true;
            }
//            return ($packRequest->getData(PackRequestInterface::STATUS) == PackRequestInterface::STATUS_PACKED) ? true : false;
        }
        return false;
    }

    /**
     * get tracking number for pack request from package
     * @return mixed
     */
    public function getTrackingCarriers()
    {
        $packRequest = $this->_coreRegistry->registry('current_pack_request');
        if ($packRequest && $packRequest->getId()) {
            $trackingCarrier = $this->packRequestService->getTrackingCarriers($packRequest->getId());
            return $trackingCarrier;
        }
        return null;
    }
}
