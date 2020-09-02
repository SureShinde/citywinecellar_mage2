<?php
/**
 * Copyright © 2016 Magestore. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magestore\DropshipSuccess\Block\DropshipRequest\Detail;

use Magestore\DropshipSuccess\Service\DropshipRequestService;

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
     * @var DropshipRequestService
     */
    protected $dropshipRequestService;

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
        DropshipRequestService $dropshipRequestService,
        array $data = []
    ) {
        parent::__construct($context, $shippingConfig, $registry, $data);
        $this->_carrierFactory = $carrierFactory;
        $this->dropshipRequestService = $dropshipRequestService;
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
     * get tracking number for dropship request from dropship shipment
     * @return mixed
     */
    public function getTrackingCarriers()
    {
        $dropshipRequest = $this->_coreRegistry->registry('current_dropship_request');
        if ($dropshipRequest && $dropshipRequest->getId()) {
            $trackingCarrier = $this->dropshipRequestService->getTrackingCarriers($dropshipRequest->getId());
            return $trackingCarrier;
        }
        return null;
    }

    /**
     * @return bool
     */
    public function isShippedRequest()
    {
        return $this->dropshipRequestService->isShippedRequest();
    }
}
